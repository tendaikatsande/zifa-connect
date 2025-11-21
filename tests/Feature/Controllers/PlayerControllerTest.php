<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $clubAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with all permissions
        $this->admin = User::factory()->create();
        $adminRole = Role::where('name', 'super_admin')->first();
        $this->admin->roles()->attach($adminRole);

        // Create club admin with limited permissions
        $this->clubAdmin = User::factory()->create();
        $clubAdminRole = Role::where('name', 'club_admin')->first();
        $this->clubAdmin->roles()->attach($clubAdminRole);
    }

    public function test_can_list_players_with_permission(): void
    {
        Player::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/players');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'status']
                ],
                'meta' => ['current_page', 'per_page', 'total']
            ]);
    }

    public function test_cannot_list_players_without_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/players');

        $response->assertStatus(403);
    }

    public function test_can_search_players(): void
    {
        Player::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        Player::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/players?search=John');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_pagination_is_limited_to_100(): void
    {
        Player::factory()->count(150)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/players?per_page=200');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_can_create_player(): void
    {
        $club = Club::factory()->create();

        $playerData = [
            'first_name' => 'Test',
            'last_name' => 'Player',
            'dob' => '1995-05-15',
            'gender' => 'M',
            'nationality' => 'Zimbabwean',
            'registration_category' => 'senior',
            'current_club_id' => $club->id,
        ];

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->postJson('/api/v1/players', $playerData);

        $response->assertStatus(201)
            ->assertJsonPath('first_name', 'Test')
            ->assertJsonPath('status', 'draft');

        $this->assertDatabaseHas('players', [
            'first_name' => 'Test',
            'last_name' => 'Player',
        ]);
    }

    public function test_validates_player_creation_data(): void
    {
        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->postJson('/api/v1/players', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'dob', 'gender', 'nationality', 'registration_category']);
    }

    public function test_can_view_player_details(): void
    {
        $player = Player::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/players/{$player->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $player->id);
    }

    public function test_can_update_draft_player(): void
    {
        $player = Player::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->patchJson("/api/v1/players/{$player->id}", [
                'first_name' => 'Updated',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('first_name', 'Updated');
    }

    public function test_cannot_update_approved_player(): void
    {
        $player = Player::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->patchJson("/api/v1/players/{$player->id}", [
                'first_name' => 'Updated',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Cannot edit player in current status');
    }

    public function test_can_delete_draft_player(): void
    {
        $player = Player::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/players/{$player->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('players', ['id' => $player->id]);
    }

    public function test_cannot_delete_non_draft_player(): void
    {
        $player = Player::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/players/{$player->id}");

        $response->assertStatus(422);
    }

    public function test_can_upload_valid_document(): void
    {
        Storage::fake('public');
        $player = Player::factory()->create();

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/documents", [
                'type' => 'photo',
                'file' => UploadedFile::fake()->image('photo.jpg', 800, 600),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('type', 'photo');

        $this->assertDatabaseHas('player_documents', [
            'player_id' => $player->id,
            'type' => 'photo',
        ]);
    }

    public function test_rejects_invalid_file_type(): void
    {
        Storage::fake('public');
        $player = Player::factory()->create();

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/documents", [
                'type' => 'photo',
                'file' => UploadedFile::fake()->create('script.php', 100),
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_approve_player(): void
    {
        $player = Player::factory()->create(['status' => 'under_review']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/approve");

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'zifa_id']);

        $player->refresh();
        $this->assertEquals('approved', $player->status);
        $this->assertNotNull($player->zifa_id);
    }

    public function test_club_admin_cannot_approve_player(): void
    {
        $player = Player::factory()->create(['status' => 'under_review']);

        $response = $this->actingAs($this->clubAdmin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/approve");

        $response->assertStatus(403);
    }

    public function test_admin_can_reject_player(): void
    {
        $player = Player::factory()->create(['status' => 'under_review']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/reject", [
                'reason' => 'Missing documents',
            ]);

        $response->assertStatus(200);

        $player->refresh();
        $this->assertEquals('rejected', $player->status);
    }

    public function test_requires_reason_when_rejecting(): void
    {
        $player = Player::factory()->create(['status' => 'under_review']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/players/{$player->id}/reject", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }

    public function test_filter_by_status(): void
    {
        Player::factory()->create(['status' => 'draft']);
        Player::factory()->create(['status' => 'approved']);
        Player::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/players?status=approved');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_filter_by_club(): void
    {
        $club = Club::factory()->create();
        Player::factory()->create(['current_club_id' => $club->id]);
        Player::factory()->create(['current_club_id' => $club->id]);
        Player::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/players?club_id={$club->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
