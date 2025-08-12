<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('solar-systems.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Explore Solar Systems
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Active Games -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">My Active Games ({{ $activeGames->count() }})</h3>
                    
                    @forelse($activeGames as $game)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold">{{ $game->name }}</h4>
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $game->status_color }}-100 text-{{ $game->status_color }}-800">
                                            {{ ucfirst($game->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex gap-4 text-sm text-gray-500 mb-2">
                                        <span>System: {{ $game->solarSystem->name }}</span>
                                        <span>Host: {{ $game->host->name }}</span>
                                        <span>Players: {{ $game->current_players }}/{{ $game->solarSystem->max_players }}</span>
                                    </div>
                                    
                                    @if($game->last_activity_at)
                                        <p class="text-xs text-gray-400">Last activity: {{ $game->last_activity_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('games.show', $game) }}" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        View Game
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">You're not currently in any active games.</p>
                            <a href="{{ route('solar-systems.index') }}" class="text-blue-500 hover:text-blue-700">
                                Browse available solar systems to join a game →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Hosted Games -->
            @if($hostedGames->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Games I'm Hosting ({{ $hostedGames->count() }})</h3>
                    
                    @foreach($hostedGames as $game)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold">{{ $game->name }}</h4>
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $game->status_color }}-100 text-{{ $game->status_color }}-800">
                                            {{ ucfirst($game->status) }}
                                        </span>
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Host
                                        </span>
                                    </div>
                                    
                                    <div class="flex gap-4 text-sm text-gray-500 mb-2">
                                        <span>System: {{ $game->solarSystem->name }}</span>
                                        <span>Players: {{ $game->current_players }}/{{ $game->solarSystem->max_players }}</span>
                                        <span>Code: {{ $game->join_code }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('games.show', $game) }}" 
                                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Manage Game
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pending Invitations -->
            @if($pendingInvitations->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Pending Invitations ({{ $pendingInvitations->count() }})</h3>
                    
                    @foreach($pendingInvitations as $invitation)
                        <div class="border border-orange-200 bg-orange-50 rounded-lg p-4 mb-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold mb-1">{{ $invitation->game->name }}</h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Invited by <strong>{{ $invitation->inviter->name }}</strong> to join 
                                        <strong>{{ $invitation->game->solarSystem->name }}</strong>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Expires {{ $invitation->expires_at->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <div class="flex gap-2">
                                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Accept
                                    </button>
                                    <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
