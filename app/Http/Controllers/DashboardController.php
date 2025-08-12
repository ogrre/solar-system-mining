<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $activeGames = $user->games()
            ->wherePivot('status', 'joined')
            ->whereIn('status', ['waiting', 'active', 'paused'])
            ->with(['solarSystem', 'host'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $pendingInvitations = $user->pendingInvitations()
            ->with(['game.solarSystem', 'inviter'])
            ->latest()
            ->get();

        $hostedGames = $user->hostedGames()
            ->whereIn('status', ['waiting', 'active', 'paused'])
            ->with('solarSystem')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        return view('dashboard', compact('activeGames', 'pendingInvitations', 'hostedGames'));
    }
}
