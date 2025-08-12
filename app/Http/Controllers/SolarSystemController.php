<?php

namespace App\Http\Controllers;

use App\Models\SolarSystem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolarSystemController extends Controller
{
    public function index(): View
    {
        $solarSystems = SolarSystem::where('is_active', true)
            ->withCount(['games as available_games_count' => function ($query) {
                $query->where('status', 'waiting')->where('is_public', true);
            }])
            ->withCount(['games as total_players_count' => function ($query) {
                $query->whereIn('status', ['active', 'paused']);
            }])
            ->get();

        return view('solar-systems.index', compact('solarSystems'));
    }

    public function show(SolarSystem $solarSystem): View
    {
        $solarSystem->loadCount([
            'games as available_games_count' => function ($query) {
                $query->where('status', 'waiting')->where('is_public', true);
            },
            'games as active_games_count' => function ($query) {
                $query->whereIn('status', ['active', 'paused']);
            }
        ]);

        $availableGames = $solarSystem->games()
            ->where('status', 'waiting')
            ->where('is_public', true)
            ->with(['host', 'solarSystem'])
            ->latest()
            ->paginate(10);

        return view('solar-systems.show', compact('solarSystem', 'availableGames'));
    }
}
