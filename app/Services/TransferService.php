<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Club;
use App\Models\Transfer;
use App\Models\TransferHistory;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

class TransferService
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    public function initiateTransfer(
        Player $player,
        int $toClubId,
        string $type,
        float $transferFee,
        ?string $notes,
        User $requestedBy
    ): Transfer {
        $transfer = Transfer::create([
            'transfer_reference' => $this->generateTransferReference(),
            'player_id' => $player->id,
            'from_club_id' => $player->current_club_id,
            'to_club_id' => $toClubId,
            'type' => $type,
            'transfer_window' => $this->getCurrentTransferWindow(),
            'requested_by' => $requestedBy->id,
            'status' => $player->current_club_id ? 'pending_from_club' : 'pending_payment',
            'transfer_fee_usd' => $transferFee,
            'admin_fee_usd' => $type === 'international'
                ? config('zifa.transfer.international_admin_fee_usd')
                : config('zifa.transfer.local_admin_fee_usd'),
        ]);

        // Create invoice for transfer fee
        if ($transfer->admin_fee_usd > 0) {
            $this->createTransferInvoice($transfer);
        }

        return $transfer;
    }

    public function isTransferWindowOpen(): bool
    {
        $windows = config('zifa.transfer.windows');
        $today = now();

        foreach ($windows as $window) {
            $start = Carbon::createFromFormat('m-d', $window['start'])->year($today->year);
            $end = Carbon::createFromFormat('m-d', $window['end'])->year($today->year);

            if ($today->between($start, $end)) {
                return true;
            }
        }

        return false;
    }

    public function getCurrentTransferWindow(): string
    {
        $month = now()->month;
        return $month <= 6 ? now()->year . '_summer' : now()->year . '_winter';
    }

    public function approveByClub(Transfer $transfer, User $approver): void
    {
        $transfer->update([
            'status' => 'pending_payment',
            'from_club_approved_by' => $approver->id,
            'from_club_approved_at' => now(),
        ]);
    }

    public function approveByZifa(Transfer $transfer, User $approver): void
    {
        // Update transfer
        $transfer->update([
            'status' => 'completed',
            'zifa_approved_by' => $approver->id,
            'zifa_approved_at' => now(),
            'effective_date' => now(),
            'certificate_url' => $this->generateTransferCertificate($transfer),
        ]);

        // Update player's club
        $player = $transfer->player;
        $oldClubId = $player->current_club_id;

        $player->update([
            'current_club_id' => $transfer->to_club_id,
        ]);

        // Record transfer history
        if ($oldClubId) {
            TransferHistory::where('player_id', $player->id)
                ->whereNull('left_date')
                ->update(['left_date' => now()]);
        }

        TransferHistory::create([
            'player_id' => $player->id,
            'club_id' => $transfer->to_club_id,
            'joined_date' => now(),
            'transfer_type' => $transfer->type,
            'transfer_id' => $transfer->id,
        ]);

        // Queue FIFA sync if international
        if ($transfer->type === 'international') {
            $this->registrationService->queueFifaSync('transfer', $transfer->id, 'create');
        }
    }

    public function reject(Transfer $transfer, string $reason, User $rejectedBy): void
    {
        $transfer->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    private function createTransferInvoice(Transfer $transfer): Invoice
    {
        $toClub = Club::find($transfer->to_club_id);

        return Invoice::create([
            'invoice_number' => sprintf('INV-%s-%06d', date('Ymd'), Invoice::whereDate('created_at', today())->count() + 1),
            'entity_type' => 'transfer',
            'entity_id' => $transfer->id,
            'description' => "Transfer Fee - {$transfer->transfer_reference}",
            'category' => 'transfer',
            'amount_cents' => ($transfer->admin_fee_usd + $transfer->transfer_fee_usd) * 100,
            'currency' => 'USD',
            'status' => 'sent',
            'due_date' => now()->addDays(7),
            'issued_to_club_id' => $transfer->to_club_id,
        ]);
    }

    private function generateTransferReference(): string
    {
        $count = Transfer::whereDate('created_at', today())->count() + 1;
        return sprintf('TRF-%s-%05d', date('Ymd'), $count);
    }

    private function generateTransferCertificate(Transfer $transfer): string
    {
        // In production, this would generate a PDF certificate
        // For now, return a placeholder URL
        return "/certificates/transfers/{$transfer->transfer_reference}.pdf";
    }
}
