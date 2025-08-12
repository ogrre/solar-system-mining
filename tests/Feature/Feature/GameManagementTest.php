<?php

namespace Tests\Feature\Feature;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected SolarSystem $solarSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->solarSystem = SolarSystem::factory()->create();
    }

    public function test_user_can_view_game_creation_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('games.create', $this->solarSystem));

        $response->assertOk();
        $response->assertSee('Create');
        $response->assertSee($this->solarSystem->name);
    }

    public function test_user_can_create_a_new_game(): void
    {
        $gameData = [
            'name' => 'Test Mining Operation',
            'description' => 'A test game description',
            'is_public' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->solarSystem), $gameData);

        $this->assertDatabaseHas('games', [
            'name' => 'Test Mining Operation',
            'description' => 'A test game description',
            'solar_system_id' => $this->solarSystem->id,
            'host_user_id' => $this->user->id,
            'current_players' => 1,
            'status' => 'waiting',
            'is_public' => true,
        ]);

        $game = Game::where('name', 'Test Mining Operation')->first();
        
        $this->assertDatabaseHas('game_players', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'status' => 'joined',
        ]);

        $response->assertRedirect(route('games.show', $game));
    }

    public function test_game_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->solarSystem), [
                'name' => '', // Invalid: empty name
                'description' => str_repeat('a', 1001), // Invalid: too long
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_can_view_game_details(): void
    {
        $game = Game::factory()->create([
            'solar_system_id' => $this->solarSystem->id,
            'host_user_id' => $this->user->id,
            'name' => 'Test Game',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('games.show', $game));

        $response->assertOk();
        $response->assertSee('Test Game');
        $response->assertSee($this->solarSystem->name);
    }

    public function test_user_can_join_public_game(): void
    {
        $host = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $this->solarSystem->id,
            'host_user_id' => $host->id,
            'current_players' => 1,
            'status' => 'waiting',
            'is_public' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('games.join-public', $game));

        $this->assertDatabaseHas('game_players', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'status' => 'joined',
        ]);

        $game->refresh();
        $this->assertEquals(2, $game->current_players);

        $response->assertRedirect(route('games.show', $game));
    }

    public function test_user_can_join_game_with_code(): void
    {
        $host = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $this->solarSystem->id,
            'host_user_id' => $host->id,
            'current_players' => 1,
            'status' => 'waiting',
            'join_code' => 'TESTCODE',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('games.join'), ['join_code' => 'TESTCODE']);

        $this->assertDatabaseHas('game_players', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'status' => 'joined',
        ]);

        $response->assertRedirect(route('games.show', $game));
    }

    public function test_user_cannot_join_game_with_invalid_code(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('games.join'), ['join_code' => 'INVALID1']);

        $response->assertSessionHasErrors(['join_code']);
    }

    public function test_user_cannot_join_full_game(): void
    {
        $solarSystem = SolarSystem::factory()->create(['max_players' => 2]);
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'current_players' => 2, // Already full
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('games.join-public', $game));

        $response->assertSessionHasErrors(['error']);
        
        $this->assertDatabaseMissing('game_players', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_cannot_join_active_game(): void
    {
        $game = Game::factory()->create([
            'solar_system_id' => $this->solarSystem->id,
            'status' => 'active', // Not waiting
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('games.join-public', $game));

        $response->assertSessionHasErrors(['error']);
    }

    public function test_user_can_leave_game(): void
    {
        $game = Game::factory()->create([
            'solar_system_id' => $this->solarSystem->id,
            'current_players' => 1,
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'joined_at' => now(),
            'status' => 'joined',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('games.leave', $game));

        $this->assertDatabaseHas('game_players', [
            'game_id' => $game->id,
            'user_id' => $this->user->id,
            'status' => 'left',
        ]);

        $game->refresh();
        $this->assertEquals(0, $game->current_players);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_leave_game_they_are_not_in(): void
    {
        $game = Game::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('games.leave', $game));

        $response->assertSessionHasErrors(['error']);
    }
}
