<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transfer;
use App\Models\Player;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService
{
    /**
     * Generate payment receipt PDF
     */
    public function generateReceipt(Payment $payment): string
    {
        $payment->load(['invoice', 'initiatedBy']);

        $data = [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'generated_at' => now(),
            'receipt_number' => 'RCP-' . date('Ymd') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
        ];

        $pdf = Pdf::loadView('pdfs.receipt', $data);

        $filename = "receipts/receipt_{$payment->payment_reference}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoice(Invoice $invoice): string
    {
        $invoice->load(['club', 'payments']);

        $data = [
            'invoice' => $invoice,
            'club' => $invoice->club,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('pdfs.invoice', $data);

        $filename = "invoices/invoice_{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate transfer certificate PDF
     */
    public function generateTransferCertificate(Transfer $transfer): string
    {
        $transfer->load(['player', 'fromClub', 'toClub']);

        $data = [
            'transfer' => $transfer,
            'player' => $transfer->player,
            'from_club' => $transfer->fromClub,
            'to_club' => $transfer->toClub,
            'generated_at' => now(),
            'certificate_number' => $transfer->transfer_certificate_number,
        ];

        $pdf = Pdf::loadView('pdfs.transfer-certificate', $data);

        $filename = "certificates/transfer_{$transfer->transfer_certificate_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate player registration card PDF
     */
    public function generatePlayerCard(Player $player): string
    {
        $player->load(['currentClub']);

        $data = [
            'player' => $player,
            'club' => $player->currentClub,
            'generated_at' => now(),
            'valid_until' => now()->endOfYear(),
        ];

        $pdf = Pdf::loadView('pdfs.player-card', $data);
        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // ID card size

        $filename = "cards/player_{$player->zifa_id}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate competition standings PDF
     */
    public function generateStandings(\App\Models\Competition $competition): string
    {
        $standings = \App\Models\CompetitionTeam::where('competition_id', $competition->id)
            ->with('club')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->get();

        $data = [
            'competition' => $competition,
            'standings' => $standings,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('pdfs.standings', $data);

        $filename = "standings/standings_{$competition->id}_" . date('Ymd') . ".pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }

    /**
     * Generate match report PDF
     */
    public function generateMatchReport(\App\Models\Match $match): string
    {
        $match->load(['homeClub', 'awayClub', 'events', 'squads.player', 'referee']);

        $data = [
            'match' => $match,
            'events' => $match->events->sortBy('minute'),
            'home_squad' => $match->squads->where('club_id', $match->home_club_id),
            'away_squad' => $match->squads->where('club_id', $match->away_club_id),
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('pdfs.match-report', $data);

        $filename = "reports/match_{$match->id}_report.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Storage::disk('public')->url($filename);
    }
}
