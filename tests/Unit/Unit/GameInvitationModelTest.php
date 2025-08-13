<?php

namespace Tests\Unit\Unit;

use App\Models\Game;
use App\Models\GameInvitation;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameInvitationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_invitation_belongs_to_game(): void
    {
        $solarSystem = SolarSystem::factory()->create();
        $game = Game::factory()->create(['solar_system_id' => $solarSystem->id]);
        $invitation = GameInvitation::factory()->create(['game_id' => $game->id]);

        $this->assertInstanceOf(Game::class, $invitation->game);
        $this->assertEquals($game->id, $invitation->game->id);
    }

    public function test_game_invitation_belongs_to_inviter(): void
    {
        $inviter = User::factory()->create();
        $invitation = GameInvitation::factory()->create(['inviter_user_id' => $inviter->id]);

        $this->assertInstanceOf(User::class, $invitation->inviter);
        $this->assertEquals($inviter->id, $invitation->inviter->id);
    }

    public function test_game_invitation_belongs_to_invited_user(): void
    {
        $invitedUser = User::factory()->create();
        $invitation = GameInvitation::factory()->create(['invited_user_id' => $invitedUser->id]);

        $this->assertInstanceOf(User::class, $invitation->invitedUser);
        $this->assertEquals($invitedUser->id, $invitation->invitedUser->id);
    }

    public function test_game_invitation_has_correct_fillable_attributes(): void
    {
        $invitation = new GameInvitation;
        $expectedFillable = [
            'game_id',
            'inviter_user_id',
            'invited_user_id',
            'email',
            'status',
            'message',
            'expires_at',
            'responded_at',
        ];

        $this->assertEquals($expectedFillable, $invitation->getFillable());
    }

    public function test_game_invitation_casts_dates_properly(): void
    {
        $invitation = GameInvitation::factory()->create([
            'expires_at' => '2024-12-31 23:59:59',
            'responded_at' => '2024-01-01 12:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $invitation->expires_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invitation->responded_at);
    }
}
