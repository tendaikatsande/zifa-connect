<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TransferService;
use App\Models\Player;
use App\Models\Club;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TransferService::class);
    }

    public function test_transfer_window_open_in_january(): void
    {
        // Mock January date
        $this->travelTo(now()->setMonth(1)->setDay(15));

        $this->assertTrue($this->service->isTransferWindowOpen());
    }

    public function test_transfer_window_open_in_july(): void
    {
        // Mock July date
        $this->travelTo(now()->setMonth(7)->setDay(15));

        $this->assertTrue($this->service->isTransferWindowOpen());
    }

    public function test_transfer_window_closed_in_march(): void
    {
        // Mock March date
        $this->travelTo(now()->setMonth(3)->setDay(15));

        $this->assertFalse($this->service->isTransferWindowOpen());
    }

    public function test_initiates_local_transfer(): void
    {
        $fromClub = Club::factory()->create();
        $toClub = Club::factory()->create();
        $player = Player::factory()->create([
            'current_club_id' => $fromClub->id,
            'status' => 'approved',
        ]);

        // Set transfer window open
        $this->travelTo(now()->setMonth(1)->setDay(15));

        $transfer = $this->service->initiateTransfer([
            'player_id' => $player->id,
            'from_club_id' => $fromClub->id,
            'to_club_id' => $toClub->id,
            'type' => 'local',
            'transfer_fee_cents' => 100000,
        ]);

        $this->assertInstanceOf(Transfer::class, $transfer);
        $this->assertEquals('local', $transfer->type);
        $this->assertEquals('pending_from_club', $transfer->status);
        $this->assertEquals(10000, $transfer->admin_fee_cents); // $100 for local
    }

    public function test_initiates_international_transfer(): void
    {
        $fromClub = Club::factory()->create();
        $toClub = Club::factory()->create();
        $player = Player::factory()->create([
            'current_club_id' => $fromClub->id,
            'status' => 'approved',
        ]);

        $this->travelTo(now()->setMonth(1)->setDay(15));

        $transfer = $this->service->initiateTransfer([
            'player_id' => $player->id,
            'from_club_id' => $fromClub->id,
            'to_club_id' => $toClub->id,
            'type' => 'international',
            'transfer_fee_cents' => 500000,
        ]);

        $this->assertEquals(50000, $transfer->admin_fee_cents); // $500 for international
    }

    public function test_club_approval_advances_status(): void
    {
        $transfer = Transfer::factory()->create([
            'status' => 'pending_from_club',
        ]);

        $this->service->approveByClub($transfer);

        $this->assertEquals('pending_payment', $transfer->fresh()->status);
    }

    public function test_zifa_approval_completes_transfer(): void
    {
        $fromClub = Club::factory()->create();
        $toClub = Club::factory()->create();
        $player = Player::factory()->create([
            'current_club_id' => $fromClub->id,
        ]);
        $transfer = Transfer::factory()->create([
            'player_id' => $player->id,
            'from_club_id' => $fromClub->id,
            'to_club_id' => $toClub->id,
            'status' => 'pending_zifa_review',
        ]);

        $this->service->approveByZifa($transfer);

        $transfer->refresh();
        $this->assertEquals('completed', $transfer->status);
        $this->assertNotNull($transfer->transfer_certificate_number);

        // Check player was moved to new club
        $this->assertEquals($toClub->id, $player->fresh()->current_club_id);
    }

    public function test_reject_transfer(): void
    {
        $transfer = Transfer::factory()->create([
            'status' => 'pending_zifa_review',
        ]);

        $this->service->reject($transfer, 'Invalid documentation');

        $transfer->refresh();
        $this->assertEquals('rejected', $transfer->status);
        $this->assertEquals('Invalid documentation', $transfer->rejection_reason);
    }

    public function test_cannot_initiate_transfer_outside_window(): void
    {
        $this->travelTo(now()->setMonth(3)->setDay(15));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transfer window is closed');

        $this->service->initiateTransfer([
            'player_id' => 1,
            'from_club_id' => 1,
            'to_club_id' => 2,
            'type' => 'local',
        ]);
    }
}
