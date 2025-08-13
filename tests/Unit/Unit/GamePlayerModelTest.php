<?php

namespace Tests\Unit\Unit;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamePlayerModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_player_belongs_to_game(): void
    {
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create(['solar_system_id' => $solarSystem->id]);
        $gamePlayer = GamePlayer::factory()->create(['game_id' => $game->id]);

        $this->assertInstanceOf(Game::class, $gamePlayer->game);
        $this->assertEquals($game->id, $gamePlayer->game->id);
    }

    public function test_game_player_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $gamePlayer = GamePlayer::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $gamePlayer->user);
        $this->assertEquals($user->id, $gamePlayer->user->id);
    }

    public function test_game_player_has_correct_fillable_attributes(): void
    {
        $gamePlayer = new GamePlayer;
        $expectedFillable = [
            'game_id',
            'user_id',
            'status',
            'player_data',
            'joined_at',
            'left_at',
        ];

        $this->assertEquals($expectedFillable, $gamePlayer->getFillable());
    }

    public function test_game_player_casts_attributes_properly(): void
    {
        $gamePlayer = GamePlayer::factory()->create([
            'player_data' => ['role' => 'miner', 'level' => 5],
            'joined_at' => '2024-01-01 12:00:00',
        ]);

        $this->assertIsArray($gamePlayer->player_data);
        $this->assertEquals('miner', $gamePlayer->player_data['role']);
        $this->assertEquals(5, $gamePlayer->player_data['level']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $gamePlayer->joined_at);
    }

    public function test_game_player_status_default(): void
    {
        $gamePlayer = GamePlayer::factory()->create();

        $this->assertEquals('joined', $gamePlayer->status);
    }
}
