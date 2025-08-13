<?php

namespace Tests\Unit\Unit;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_can_join_method_returns_true_for_valid_conditions(): void
    {
        $solarSystem = SolarSystem::factory()->create(['max_players' => 4]);
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'status' => 'waiting',
            'current_players' => 2,
        ]);

        $this->assertTrue($game->canJoin($user));
    }

    public function test_game_can_join_method_returns_false_when_game_is_not_waiting(): void
    {
        $solarSystem = SolarSystem::factory()->create(['max_players' => 4]);
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'status' => 'active',
            'current_players' => 2,
        ]);

        $this->assertFalse($game->canJoin($user));
    }

    public function test_game_can_join_method_returns_false_when_game_is_full(): void
    {
        $solarSystem = SolarSystem::factory()->create(['max_players' => 2]);
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'status' => 'waiting',
            'current_players' => 2, // Already at max capacity
        ]);

        $this->assertFalse($game->canJoin($user));
    }

    public function test_game_can_join_method_returns_false_when_user_already_joined(): void
    {
        $solarSystem = SolarSystem::factory()->create(['max_players' => 4]);
        $user = User::factory()->create();
        $game = Game::factory()->create([
            'solar_system_id' => $solarSystem->id,
            'status' => 'waiting',
            'current_players' => 1,
        ]);

        // User already joined
        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        $this->assertFalse($game->canJoin($user));
    }

    public function test_game_generates_join_code_automatically(): void
    {
        $game = Game::factory()->create();

        $this->assertNotNull($game->join_code);
        $this->assertEquals(8, strlen($game->join_code));
        $this->assertTrue(ctype_upper($game->join_code));
    }

    public function test_game_status_color_attribute_returns_correct_colors(): void
    {
        $testCases = [
            'waiting' => 'blue',
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'gray',
            'abandoned' => 'red',
        ];

        foreach ($testCases as $status => $expectedColor) {
            $game = Game::factory()->create(['status' => $status]);
            $this->assertEquals($expectedColor, $game->status_color);
        }
    }

    public function test_game_belongs_to_solar_system(): void
    {
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create(['solar_system_id' => $solarSystem->id]);

        $this->assertInstanceOf(SolarSystem::class, $game->solarSystem);
        $this->assertEquals($solarSystem->id, $game->solarSystem->id);
    }

    public function test_game_belongs_to_host_user(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['host_user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $game->host);
        $this->assertEquals($user->id, $game->host->id);
    }

    public function test_game_has_many_game_players(): void
    {
        $game = Game::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user1->id,
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user2->id,
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        $this->assertCount(2, $game->gamePlayers);
    }

    public function test_game_active_players_only_returns_joined_players(): void
    {
        $game = Game::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user1->id,
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => $user2->id,
            'status' => 'left',
            'joined_at' => now(),
            'left_at' => now(),
        ]);

        $this->assertCount(1, $game->activePlayers);
        /** @var \App\Models\GamePlayer $activePlayer */
        $activePlayer = $game->activePlayers->first();
        $this->assertEquals($user1->id, $activePlayer->user_id);
    }
}
