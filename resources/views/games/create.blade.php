<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New Game in {{ $solarSystem->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('games.store', $solarSystem) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Game Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_public" 
                                       value="1" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                                       {{ old('is_public', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Make this game public</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Public games can be joined by anyone. Private games require an invitation or join code.</p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('solar-systems.show', $solarSystem) }}" 
                               class="px-4 py-2 text-gray-500 hover:text-gray-700">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Game
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>