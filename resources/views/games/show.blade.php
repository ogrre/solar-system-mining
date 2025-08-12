<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $game->name }}
            </h2>
            <a href="{{ route('solar-systems.show', $game->solarSystem) }}" class="text-blue-500 hover:text-blue-700">
                ← Back to {{ $game->solarSystem->name }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Game Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-2xl font-bold">{{ $game->name }}</h3>
                                <span class="px-3 py-1 rounded text-sm font-medium bg-{{ $game->status_color }}-100 text-{{ $game->status_color }}-800">
                                    {{ ucfirst($game->status) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-4">{{ $game->description ?: 'No description provided.' }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <div class="text-lg font-bold text-blue-600">{{ $game->current_players }}</div>
                                    <div class="text-sm text-gray-500">Current Players</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-green-600">{{ $game->solarSystem->max_players }}</div>
                                    <div class="text-sm text-gray-500">Max Players</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-purple-600">{{ $game->host->name }}</div>
                                    <div class="text-sm text-gray-500">Host</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-orange-600">{{ $game->join_code }}</div>
                                    <div class="text-sm text-gray-500">Join Code</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Game Actions -->
                    <div class="flex gap-3">
                        @if($game->canJoin(auth()->user()))
                            <form action="{{ route('games.join-public', $game) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Join Game
                                </button>
                            </form>
                        @endif

                        @if($game->activePlayers()->where('user_id', auth()->id())->exists())
                            <form action="{{ route('games.leave', $game) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Are you sure you want to leave this game?')">
                                    Leave Game
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Players List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Players ({{ $game->activePlayers->count() }})</h3>
                    
                    @forelse($game->activePlayers as $gamePlayer)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr($gamePlayer->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $gamePlayer->user->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        Joined {{ $gamePlayer->joined_at->diffForHumans() }}
                                        @if($gamePlayer->user_id === $game->host_user_id)
                                            <span class="text-yellow-600 font-medium">(Host)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No active players yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>