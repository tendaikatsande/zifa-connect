<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Club;
use App\Models\Registration;
use App\Models\Affiliation;
use App\Models\Invoice;
use App\Models\FifaSyncQueue;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function createPlayerRegistration(Player $player): Registration
    {
        $registrationNumber = $this->generateRegistrationNumber('player');

        return Registration::create([
            'registration_number' => $registrationNumber,
            'entity_type' => 'player',
            'entity_id' => $player->id,
            'season' => config('zifa.seasons.current'),
            'status' => 'pending_payment',
            'submitted_by' => $player->created_by,
        ]);
    }

    public function createRegistrationInvoice(Registration $registration): Invoice
    {
        $fee = match ($registration->entity_type) {
            'player' => config('zifa.registration.player.fee_usd'),
            'club' => config('zifa.registration.club.affiliation_fee_usd'),
            'official' => config('zifa.registration.official.fee_usd'),
            'referee' => config('zifa.registration.referee.fee_usd'),
            default => 0,
        };

        $invoiceNumber = $this->generateInvoiceNumber();

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'entity_type' => 'registration',
            'entity_id' => $registration->id,
            'description' => ucfirst($registration->entity_type) . " Registration - {$registration->registration_number}",
            'category' => 'registration',
            'amount_cents' => $fee * 100,
            'currency' => 'USD',
            'status' => 'sent',
            'due_date' => now()->addDays(14),
            'issued_to_club_id' => $registration->entity_type === 'player'
                ? Player::find($registration->entity_id)?->current_club_id
                : null,
        ]);
    }

    public function createAffiliation(Club $club): Affiliation
    {
        return Affiliation::create([
            'club_id' => $club->id,
            'season' => config('zifa.seasons.current'),
            'status' => 'pending',
            'payment_status' => 'pending',
            'expiry_date' => now()->endOfYear(),
        ]);
    }

    public function createAffiliationInvoice(Affiliation $affiliation): Invoice
    {
        $fee = config('zifa.registration.club.affiliation_fee_usd');
        $invoiceNumber = $this->generateInvoiceNumber();

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'entity_type' => 'affiliation',
            'entity_id' => $affiliation->id,
            'description' => "Club Affiliation - Season {$affiliation->season}",
            'category' => 'affiliation',
            'amount_cents' => $fee * 100,
            'currency' => 'USD',
            'status' => 'sent',
            'due_date' => now()->addDays(30),
            'issued_to_club_id' => $affiliation->club_id,
        ]);
    }

    public function generateZifaId(string $entityType): string
    {
        $format = config("zifa.id_format.{$entityType}");
        $prefix = match ($entityType) {
            'player' => 'ZFA-P-',
            'club' => 'ZFA-C-',
            'official' => 'ZFA-O-',
            'referee' => 'ZFA-R-',
            default => 'ZFA-X-',
        };

        $lastId = DB::table(match ($entityType) {
            'player' => 'players',
            'club' => 'clubs',
            'official' => 'officials',
            'referee' => 'referees',
            default => 'users',
        })
            ->whereNotNull('zifa_id')
            ->orderByDesc('id')
            ->value('zifa_id');

        if ($lastId) {
            $number = (int) preg_replace('/\D/', '', $lastId);
            $number++;
        } else {
            $number = 1;
        }

        return sprintf($format, $number);
    }

    public function queueFifaSync(string $entityType, int $entityId, string $action): void
    {
        if (!config('fifa.sync.enabled')) {
            return;
        }

        FifaSyncQueue::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'status' => 'pending',
            'next_attempt_at' => now(),
        ]);
    }

    private function generateRegistrationNumber(string $type): string
    {
        $prefix = strtoupper(substr($type, 0, 3));
        $count = Registration::whereDate('created_at', today())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, date('Ymd'), $count);
    }

    private function generateInvoiceNumber(): string
    {
        $count = Invoice::whereDate('created_at', today())->count() + 1;
        return sprintf('INV-%s-%06d', date('Ymd'), $count);
    }
}
