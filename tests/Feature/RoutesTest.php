<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_route_redirects_to_solar_systems(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/solar-systems');
    }

    public function test_solar_systems_index_route(): void
    {
        $user = User::factory()->create();
        SolarSystem::factory()->create();

        $response = $this->actingAs($user)->get('/solar-systems');

        $response->assertStatus(200);
        $response->assertViewIs('solar-systems.index');
    }

    public function test_solar_system_show_route(): void
    {
        $user = User::factory()->create();
        $solarSystem = SolarSystem::factory()->create();

        $response = $this->actingAs($user)->get("/solar-systems/{$solarSystem->id}");

        $response->assertStatus(200);
        $response->assertViewIs('solar-systems.show');
    }

    public function test_game_create_route(): void
    {
        $user = User::factory()->create();
        $solarSystem = SolarSystem::factory()->create();

        $response = $this->actingAs($user)->get("/solar-systems/{$solarSystem->id}/games/create");

        $response->assertStatus(200);
        $response->assertViewIs('games.create');
    }

    public function test_game_store_route(): void
    {
        $user = User::factory()->create();
        $solarSystem = SolarSystem::factory()->create();

        $response = $this->actingAs($user)->post("/solar-systems/{$solarSystem->id}/games", [
            'name' => 'Test Game',
            'description' => 'Test Description',
            'is_public' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('games', ['name' => 'Test Game']);
    }

    public function test_game_show_route(): void
    {
        $user = User::factory()->create();
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create(['solar_system_id' => $solarSystem->id, 'host_user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/games/{$game->id}");

        $response->assertStatus(200);
        $response->assertViewIs('games.show');
    }

    public function test_dashboard_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_profile_routes_require_authentication(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');

        $response = $this->patch('/profile');
        $response->assertRedirect('/login');

        $response = $this->delete('/profile');
        $response->assertRedirect('/login');
    }
}
