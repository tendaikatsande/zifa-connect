<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Traits\LogsAuthorizationDenials;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    use LogsAuthorizationDenials;

    /**
     * Check if user can access this invoice
     */
    private function canAccessInvoice(Request $request, Invoice $invoice): bool
    {
        $user = $request->user();

        // Super admins and ZIFA staff can access any invoice
        if ($user->hasRole('super_admin') || $user->hasRole('zifa_admin') || $user->hasRole('zifa_finance')) {
            return true;
        }

        // User is the invoice recipient
        if ($invoice->issued_to_user_id === $user->id) {
            return true;
        }

        // User is official of the invoiced club
        if ($invoice->issued_to_club_id) {
            return $user->clubs()
                ->where('clubs.id', $invoice->issued_to_club_id)
                ->wherePivot('status', 'active')
                ->exists();
        }

        return false;
    }
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Invoice::with(['club', 'payments'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->when($request->club_id, fn($q, $clubId) => $q->where('issued_to_club_id', $clubId))
            ->when($request->overdue, fn($q) =>
                $q->where('status', '!=', 'paid')->where('due_date', '<', now())
            );

        // Non-admin users can only see their own invoices
        if (!$user->hasRole('super_admin') && !$user->hasRole('zifa_admin') && !$user->hasRole('zifa_finance')) {
            $userClubIds = $user->clubs()->wherePivot('status', 'active')->pluck('clubs.id');
            $query->where(function ($q) use ($user, $userClubIds) {
                $q->where('issued_to_user_id', $user->id)
                  ->orWhereIn('issued_to_club_id', $userClubIds);
            });
        }

        $query->orderBy('created_at', 'desc');

        $invoices = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($invoices);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        // Verify user can access this invoice
        if (!$this->canAccessInvoice($request, $invoice)) {
            $this->logResourceDenial($request, 'invoice', $invoice->id, 'view');
            return response()->json(['message' => 'Unauthorized to view this invoice'], 403);
        }

        $invoice->load(['club', 'user', 'payments']);
        $invoice->append(['total_paid', 'balance']);

        return response()->json($invoice);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'description' => 'required|string|max:500',
            'category' => 'required|string',
            'amount_cents' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'due_date' => 'required|date|after:today',
            'issued_to_club_id' => 'nullable|exists:clubs,id',
            'issued_to_user_id' => 'nullable|exists:users,id',
            'line_items' => 'nullable|array',
        ]);

        $validated['invoice_number'] = $this->generateInvoiceNumber();
        $validated['status'] = 'sent';
        $validated['created_by'] = $request->user()->id;

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    public function cancel(Request $request, Invoice $invoice): JsonResponse
    {
        // Only ZIFA staff can cancel invoices
        $user = $request->user();
        if (!$user->hasRole('super_admin') && !$user->hasRole('zifa_admin') && !$user->hasRole('zifa_finance')) {
            $this->logResourceDenial($request, 'invoice', $invoice->id, 'cancel');
            return response()->json(['message' => 'Unauthorized to cancel invoices'], 403);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Cannot cancel paid invoice'], 422);
        }

        $invoice->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Invoice cancelled']);
    }

    private function generateInvoiceNumber(): string
    {
        $count = Invoice::whereDate('created_at', today())->count() + 1;
        return sprintf('INV-%s-%06d', date('Ymd'), $count);
    }
}
