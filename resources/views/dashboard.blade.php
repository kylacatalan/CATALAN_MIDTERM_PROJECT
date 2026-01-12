<x-layouts.app :title="__('Game Dashboard')">

    @if(session('success'))
        <div class="rounded-lg bg-green-900/30 p-4 text-sm text-green-200 dark:bg-green-900/50 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <!-- Search & Filter Section -->
        <div class="rounded-xl border mb-5 border-cyan-700 bg-gray-900 p-10 dark:border-cyan-900 dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-cyan-300">
                Search & Filter Games
            </h2>

            <form action="{{ route('dashboard') }}" method="GET" class="grid gap-4 md:grid-cols-3">

                <!-- Search Input -->
                <div class="md:col-span-1">
                    <label class="mb-2 block text-sm font-medium text-cyan-200">
                        Search
                    </label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by game name"
                        class="w-full rounded-lg border border-cyan-600 bg-gray-800 px-4 py-2 text-sm
                            focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30
                            dark:border-cyan-700 dark:bg-gray-900 dark:text-white"
                    >
                </div>

                <!-- Genre Filter Dropdown -->
                <div class="md:col-span-1">
                    <label class="mb-2 block text-sm font-medium text-cyan-200">
                        Filter by Genre
                    </label>
                    <select
                        name="genre_filter"
                        class="w-full rounded-lg border border-cyan-600 bg-gray-800 px-4 py-2 text-sm
                            focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30
                            dark:border-cyan-700 dark:bg-gray-900 dark:text-white"
                    >
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                            <option
                                value="{{ $genre->id }}"
                                {{ request('genre_filter') == $genre->id ? 'selected' : '' }}
                            >
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2 md:col-span-1">
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-cyan-500"
                    >
                        Apply Filters
                    </button>

                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-lg border border-cyan-600 px-4 py-2 text-sm font-medium text-cyan-200
                            transition-colors hover:bg-cyan-700 hover:text-white"
                    >
                        Clear
                    </a>
                </div>
            </form>
        </div>

        @foreach($genres as $genre)

            {{-- GENRE LABEL --}}
            <div class="w-30 flex justify-center rounded-full bg-cyan-600 font-semibold text-white
                hover:bg-cyan-500 py-2 transition">
                {{ $genre->name }} 🎮
            </div>
        
            {{-- GENRE GAMES GRID --}}
            <div class="flex space-x-4 mt-4 overflow-x-auto pb-4 overflow-hidden">
            
                @php
                    $genreGames = $games->where('genre_id', $genre->id);
                @endphp

                @forelse($genreGames as $game)
            
                    <div x-data="{ open: false }" class="flex-shrink-0 w-64 md:w-80 rounded-xl border border-cyan-700 dark:border-cyan-900 overflow-hidden cursor-pointer shadow-lg">

                        {{-- GAME PHOTO --}}
                        <div class="relative aspect-video" @click="open = true">
                            @if($game->photo)
                                <img src="{{ asset('storage/' . $game->photo) }}"
                                     alt="{{ $game->name }}"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center 
                                    bg-gray-800 text-gray-400 dark:bg-gray-700 dark:text-gray-300">
                                    No Photo
                                </div>
                            @endif
                        </div>
                    
                        {{-- TITLE BELOW --}}
                        <div class="p-3 bg-gray-900 dark:bg-gray-800" @click="open = true">
                            <h3 class="text-md flex justify-center font-medium text-cyan-300 dark:text-white">
                                {{ $game->name }}
                            </h3>
                        </div>
                    
                        {{-- MODAL --}}
                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                             x-transition.opacity>
                            <div class="bg-gray-900 dark:bg-gray-800 rounded-xl shadow-lg max-w-4xl w-full mx-4 md:mx-0 flex overflow-hidden">

                                {{-- Left: Full Photo --}}
                                <div class="w-1/2 hidden md:block">
                                    @if($game->photo)
                                        <img src="{{ asset('storage/' . $game->photo) }}" alt="{{ $game->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full bg-gray-800 text-gray-400 dark:bg-gray-700 dark:text-gray-300">
                                            No Photo
                                        </div>
                                    @endif
                                </div>
                            
                                {{-- Right: Game Details --}}
                                <div class="w-full md:w-1/2 p-6 flex flex-col justify-center space-y-2">
                                    <h2 class="text-2xl font-bold text-cyan-300 dark:text-white mb-2">{{ $game->name }}</h2>
                                    <p class="text-gray-300 dark:text-gray-200"><strong>Genre:</strong> {{ $genre->name }}</p>
                                    <p class="text-gray-300 dark:text-gray-200"><strong>Release Year:</strong> {{ $game->release_year ?? 'N/A' }}</p>
                                    <p class="text-gray-300 dark:text-gray-200"><strong>Developer:</strong> {{ $game->developer ?? 'N/A' }}</p>
                                    <p class="text-gray-300 dark:text-gray-200"><strong>Publisher:</strong> {{ $game->publisher ?? 'N/A' }}</p>
                                    <p class="text-gray-300 dark:text-gray-200"><strong>Rating:</strong> {{ $game->rating ?? 'N/A' }}</p>
                                
                                    {{-- Close Button --}}
                                    <button @click="open = false" class="mt-4 px-4 py-2 bg-cyan-600 text-white rounded transition self-end hover:bg-cyan-500">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                    </div>

                @empty
                    {{-- IF NO GAMES IN THIS GENRE --}}
                    <p class="text-gray-400 dark:text-gray-300 col-span-3">
                        No games available for this genre.
                    </p>
                @endforelse
                
            </div>

        @endforeach

    </div>
</x-layouts.app>
