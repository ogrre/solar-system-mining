<?php

namespace Tests\Feature\Feature;

use App\Models\Game;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_dashboard_shows_hosted_games(): void
    {
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'host_user_id' => $this->user->id,
            'name' => 'My Hosted Game',
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertSee('My Hosted Game');
    }

    public function test_dashboard_does_not_show_completed_games(): void
    {
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'host_user_id' => $this->user->id,
            'name' => 'Completed Game',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertDontSee('Completed Game');
    }
}
