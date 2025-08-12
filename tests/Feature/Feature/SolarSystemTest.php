<?php

namespace Tests\Feature\Feature;

use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolarSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_redirected_to_login_when_accessing_solar_systems(): void
    {
        $response = $this->get(route('solar-systems.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_solar_systems_index(): void
    {
        SolarSystem::factory()->create([
            'name' => 'Alpha Centauri',
            'difficulty' => 'easy',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('solar-systems.index'));

        $response->assertOk();
        $response->assertSee('Alpha Centauri');
        $response->assertSee('Solar Systems');
    }

    public function test_only_active_solar_systems_are_displayed(): void
    {
        SolarSystem::factory()->create([
            'name' => 'Active System',
            'is_active' => true,
        ]);

        SolarSystem::factory()->create([
            'name' => 'Inactive System',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('solar-systems.index'));

        $response->assertSee('Active System');
        $response->assertDontSee('Inactive System');
    }

    public function test_user_can_view_solar_system_details(): void
    {
        $solarSystem = SolarSystem::factory()->create([
            'name' => 'Kepler-442',
            'description' => 'A test system',
            'difficulty' => 'medium',
            'available_resources' => ['Gold', 'Platinum'],
            'max_players' => 6,
        ]);

        $response = $this->actingAs($this->user)->get(route('solar-systems.show', $solarSystem));

        $response->assertOk();
        $response->assertSee('Kepler-442');
        $response->assertSee('A test system');
        $response->assertSee('medium');
        $response->assertSee('Gold');
        $response->assertSee('Platinum');
        $response->assertSee('6');
    }

    public function test_solar_system_shows_available_games_count(): void
    {
        $solarSystem = SolarSystem::factory()->create();

        $response = $this->actingAs($this->user)->get(route('solar-systems.show', $solarSystem));

        $response->assertOk();
        $response->assertSee('Available Games');
        $response->assertSee('Active Games');
    }

    public function test_home_page_redirects_to_solar_systems_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('solar-systems.index'));
    }
}
