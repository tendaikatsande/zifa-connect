<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\RegistrationService;
use App\Models\Player;
use App\Models\Club;
use App\Models\Registration;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RegistrationService::class);
    }

    public function test_generates_unique_zifa_id_for_player(): void
    {
        $zifaId = $this->service->generateZifaId('player');

        $this->assertStringStartsWith('ZFA-P-', $zifaId);
        $this->assertEquals(12, strlen($zifaId));
    }

    public function test_generates_unique_zifa_id_for_club(): void
    {
        $zifaId = $this->service->generateZifaId('club');

        $this->assertStringStartsWith('ZFA-C-', $zifaId);
    }

    public function test_generates_unique_zifa_id_for_official(): void
    {
        $zifaId = $this->service->generateZifaId('official');

        $this->assertStringStartsWith('ZFA-O-', $zifaId);
    }

    public function test_generates_unique_zifa_id_for_referee(): void
    {
        $zifaId = $this->service->generateZifaId('referee');

        $this->assertStringStartsWith('ZFA-R-', $zifaId);
    }

    public function test_generates_sequential_ids(): void
    {
        $id1 = $this->service->generateZifaId('player');
        $id2 = $this->service->generateZifaId('player');

        $this->assertNotEquals($id1, $id2);
    }

    public function test_creates_player_registration(): void
    {
        $club = Club::factory()->create();
        $player = Player::factory()->create([
            'current_club_id' => $club->id,
            'status' => 'draft',
        ]);

        $registration = $this->service->createPlayerRegistration($player);

        $this->assertInstanceOf(Registration::class, $registration);
        $this->assertEquals('player', $registration->entity_type);
        $this->assertEquals($player->id, $registration->entity_id);
        $this->assertEquals('pending_payment', $registration->status);
        $this->assertNotNull($registration->registration_number);
    }

    public function test_creates_registration_invoice(): void
    {
        $club = Club::factory()->create();
        $player = Player::factory()->create([
            'current_club_id' => $club->id,
        ]);
        $registration = Registration::factory()->create([
            'entity_type' => 'player',
            'entity_id' => $player->id,
            'club_id' => $club->id,
        ]);

        $invoice = $this->service->createRegistrationInvoice($registration);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('registration', $invoice->category);
        $this->assertEquals('pending', $invoice->status);
        $this->assertGreaterThan(0, $invoice->amount_cents);
    }

    public function test_queues_fifa_sync(): void
    {
        $player = Player::factory()->create();

        $result = $this->service->queueFifaSync('player', $player->id, 'create');

        $this->assertDatabaseHas('fifa_sync_queue', [
            'entity_type' => 'player',
            'entity_id' => $player->id,
            'operation' => 'create',
            'status' => 'pending',
        ]);
    }
}
