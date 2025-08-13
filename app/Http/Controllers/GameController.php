<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\SolarSystem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function create(SolarSystem $solarSystem): View
    {
        return view('games.create', compact('solarSystem'));
    }

    public function store(Request $request, SolarSystem $solarSystem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        /** @var Game $game */
        $game = $solarSystem->games()->create([
            ...$validated,
            'host_user_id' => auth()->id(),
            'current_players' => 1, // Host is first player
            'last_activity_at' => now(),
        ]);

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => auth()->id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('games.show', $game)
            ->with('success', 'Game created successfully!');
    }

    public function show(Game $game): View
    {
        $game->load(['solarSystem', 'host', 'activePlayers.user']);

        return view('games.show', compact('game'));
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'join_code' => 'required|string|size:8',
        ]);

        $game = Game::where('join_code', $validated['join_code'])->first();

        if (! $game) {
            return back()->withErrors(['join_code' => 'Game not found with this code.']);
        }

        /** @var User $user */
        $user = auth()->user();

        if (! $game->canJoin($user)) {
            return back()->withErrors(['join_code' => 'Cannot join this game.']);
        }

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => auth()->id(),
            'joined_at' => now(),
        ]);

        $game->current_players += 1;
        $game->last_activity_at = now();
        $game->save();

        return redirect()->route('games.show', $game)
            ->with('success', 'Successfully joined the game!');
    }

    public function joinPublic(Game $game): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $game->canJoin($user)) {
            return back()->withErrors(['error' => 'Cannot join this game.']);
        }

        GamePlayer::create([
            'game_id' => $game->id,
            'user_id' => auth()->id(),
            'joined_at' => now(),
        ]);

        $game->current_players += 1;
        $game->last_activity_at = now();
        $game->save();

        return redirect()->route('games.show', $game)
            ->with('success', 'Successfully joined the game!');
    }

    public function leave(Game $game): RedirectResponse
    {
        $gamePlayer = GamePlayer::where('game_id', $game->id)
            ->where('user_id', auth()->id())
            ->where('status', 'joined')
            ->first();

        if (! $gamePlayer) {
            return back()->withErrors(['error' => 'You are not in this game.']);
        }

        $gamePlayer->update([
            'status' => 'left',
            'left_at' => now(),
        ]);

        $game->current_players -= 1;
        $game->last_activity_at = now();
        $game->save();

        return redirect()->route('dashboard')
            ->with('success', 'Left the game successfully.');
    }
}
