<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $activeGames = $user->games()
            ->wherePivot('status', 'joined')
            ->whereIn('games.status', ['waiting', 'active', 'paused'])
            ->with(['solarSystem', 'host'])
            ->orderBy('games.last_activity_at', 'desc')
            ->get();

        $pendingInvitations = $user->pendingInvitations()
            ->with(['game.solarSystem', 'inviter'])
            ->latest()
            ->get();

        $hostedGames = $user->hostedGames()
            ->whereIn('games.status', ['waiting', 'active', 'paused'])
            ->with('solarSystem')
            ->orderBy('games.last_activity_at', 'desc')
            ->get();

        return view('dashboard', compact('activeGames', 'pendingInvitations', 'hostedGames'));
    }
}
