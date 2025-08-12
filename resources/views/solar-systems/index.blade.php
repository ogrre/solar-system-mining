<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Solar Systems') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-2xl font-bold">Choose Your Adventure</h3>
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            My Games
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($solarSystems as $solarSystem)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                @if($solarSystem->image_url)
                                    <img src="{{ $solarSystem->image_url }}" alt="{{ $solarSystem->name }}" class="w-full h-48 object-cover rounded-t-lg">
                                @endif
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-xl font-bold text-gray-900">{{ $solarSystem->name }}</h4>
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $solarSystem->difficulty_color }}-100 text-{{ $solarSystem->difficulty_color }}-800">
                                            {{ ucfirst($solarSystem->difficulty) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ $solarSystem->description }}</p>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Available Games:</span>
                                            <span class="font-medium">{{ $solarSystem->available_games_count ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Players Online:</span>
                                            <span class="font-medium">{{ $solarSystem->total_players_count ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Max Players:</span>
                                            <span class="font-medium">{{ $solarSystem->max_players }}</span>
                                        </div>
                                    </div>

                                    @if($solarSystem->available_resources)
                                        <div class="mb-4">
                                            <span class="text-sm text-gray-500">Resources:</span>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($solarSystem->available_resources as $resource)
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">{{ $resource }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('solar-systems.show', $solarSystem) }}" 
                                       class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center block">
                                        Explore System
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <p class="text-gray-500">No solar systems available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>