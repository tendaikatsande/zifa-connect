<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Club;
use App\Models\Transfer;
use App\Models\Invoice;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $season = $request->season ?? date('Y');

        return response()->json([
            'stats' => $this->getStats($season),
            'recent_registrations' => $this->getRecentRegistrations(),
            'pending_transfers' => $this->getPendingTransfers(),
            'revenue' => $this->getRevenue($season),
            'charts' => $this->getChartData($season),
        ]);
    }

    private function getStats(string $season): array
    {
        return [
            'total_players' => Player::where('status', 'approved')->count(),
            'active_clubs' => Club::where('status', 'active')->count(),
            'pending_registrations' => Registration::whereIn('status', ['pending_payment', 'pending_review'])->count(),
            'pending_transfers' => Transfer::whereIn('status', ['requested', 'pending_from_club', 'pending_payment', 'pending_zifa_review'])->count(),
            'total_revenue_usd' => Invoice::where('status', 'paid')
                ->whereYear('paid_date', $season)
                ->sum('amount_cents') / 100,
            'outstanding_invoices' => Invoice::whereIn('status', ['sent', 'pending', 'overdue'])->sum('amount_cents') / 100,
        ];
    }

    private function getRecentRegistrations(): array
    {
        return Registration::with(['submitter'])
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getPendingTransfers(): array
    {
        return Transfer::with(['player', 'fromClub', 'toClub'])
            ->whereIn('status', ['requested', 'pending_from_club', 'pending_payment', 'pending_zifa_review'])
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getRevenue(string $season): array
    {
        return Invoice::where('status', 'paid')
            ->whereYear('paid_date', $season)
            ->selectRaw("category, SUM(amount_cents) as total")
            ->groupBy('category')
            ->get()
            ->mapWithKeys(fn($item) => [$item->category => $item->total / 100])
            ->toArray();
    }

    private function getChartData(string $season): array
    {
        // Monthly revenue
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_date', $season)
            ->selectRaw("DATE_TRUNC('month', paid_date) as month, SUM(amount_cents) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month->format('M'),
                'total' => $item->total / 100,
            ]);

        // Registrations by category
        $registrationsByCategory = Player::where('status', 'approved')
            ->selectRaw('registration_category, COUNT(*) as count')
            ->groupBy('registration_category')
            ->get();

        return [
            'monthly_revenue' => $monthlyRevenue,
            'registrations_by_category' => $registrationsByCategory,
        ];
    }
}
