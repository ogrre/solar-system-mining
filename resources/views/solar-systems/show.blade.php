<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $solarSystem->name }}
            </h2>
            <a href="{{ route('solar-systems.index') }}" class="text-blue-500 hover:text-blue-700">
                ← Back to Solar Systems
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Solar System Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row gap-6">
                        @if($solarSystem->image_url)
                            <img src="{{ $solarSystem->image_url }}" alt="{{ $solarSystem->name }}" class="w-full md:w-1/3 h-64 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <h3 class="text-2xl font-bold">{{ $solarSystem->name }}</h3>
                                <span class="px-3 py-1 rounded text-sm font-medium bg-{{ $solarSystem->difficulty_color }}-100 text-{{ $solarSystem->difficulty_color }}-800">
                                    {{ ucfirst($solarSystem->difficulty) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-6">{{ $solarSystem->description }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $solarSystem->available_games_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-500">Available Games</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $solarSystem->active_games_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-500">Active Games</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">{{ $solarSystem->max_players }}</div>
                                    <div class="text-sm text-gray-500">Max Players</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ count($solarSystem->available_resources ?? []) }}</div>
                                    <div class="text-sm text-gray-500">Resources</div>
                                </div>
                            </div>

                            @if($solarSystem->available_resources)
                                <div class="mb-4">
                                    <h4 class="font-semibold mb-2">Available Resources:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($solarSystem->available_resources as $resource)
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ $resource }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-3">
                                <a href="{{ route('games.create', $solarSystem) }}" 
                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                    Create New Game
                                </a>
                                
                                <button onclick="document.getElementById('join-modal').classList.remove('hidden')" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Join with Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Games -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Available Games</h3>
                    
                    @forelse($availableGames as $game)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold">{{ $game->name }}</h4>
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $game->status_color }}-100 text-{{ $game->status_color }}-800">
                                            {{ ucfirst($game->status) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-2">{{ $game->description ?: 'No description provided.' }}</p>
                                    
                                    <div class="flex gap-4 text-sm text-gray-500">
                                        <span>Host: {{ $game->host->name }}</span>
                                        <span>Players: {{ $game->current_players }}/{{ $game->solarSystem->max_players }}</span>
                                        <span>Code: {{ $game->join_code }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('games.show', $game) }}" 
                                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        View
                                    </a>
                                    
                                    @if($game->canJoin(auth()->user()))
                                        <form action="{{ route('games.join-public', $game) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                Join
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No available games. Be the first to create one!</p>
                    @endforelse

                    {{ $availableGames->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Join with Code Modal -->
    <div id="join-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Join Game with Code</h3>
                
                <form action="{{ route('games.join') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="join_code" class="block text-sm font-medium text-gray-700 mb-2">Game Code</label>
                        <input type="text" 
                               name="join_code" 
                               id="join_code" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter 8-character code"
                               maxlength="8"
                               required>
                        @error('join_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" 
                                onclick="document.getElementById('join-modal').classList.add('hidden')"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Join Game
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>