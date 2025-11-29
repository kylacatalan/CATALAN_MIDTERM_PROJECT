<x-layouts.app :title="__('Game Dashboard')">
    <div class="space-y-6">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="rounded-lg bg-green-900/30 p-4 text-sm text-green-200 dark:bg-green-900/50 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Top Summary Cards --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            {{-- Card 1: Total Games --}}
            <div class="rounded-xl border border-cyan-700 bg-gray-900 p-5 shadow-lg shadow-cyan-900/20">
                <p class="text-sm font-medium text-cyan-400">Total Games</p>
                {{-- NOTE: Replace 150 with a dynamic variable like $totalGames in your controller --}}
                <p class="mt-1 text-3xl font-bold text-white">{{ $games->count() }}</p> 
            </div>
            
            {{-- Card 2: Total Genres --}}
            <div class="rounded-xl border border-cyan-700 bg-gray-900 p-5 shadow-lg shadow-cyan-900/20">
                <p class="text-sm font-medium text-cyan-400">Total Genres</p>
                {{-- NOTE: Replace 12 with a dynamic variable like $totalGenres in your controller --}}
                <p class="mt-1 text-3xl font-bold text-white">{{ $genres->count() }}</p>
            </div>
            
            {{-- Card 3: Average Rating --}}
            <div class="rounded-xl border border-cyan-700 bg-gray-900 p-5 shadow-lg shadow-cyan-900/20">
                <p class="text-sm font-medium text-cyan-400">Highest Rated Game</p>
                <div class="flex flex-row justify-between items-center">
                    @if($topGame)
                        <p class="mt-1 text-2xl font-semibold text-white">
                            {{ $topGame->name }}
                        </p>
                        <p class="text-3xl font-bold text-cyan-300">
                            {{ $topGame->rating }}
                        </p>
                    @else
                        <p class="mt-1 text-white">No games added yet.</p>
                    @endif
                </div>
            </div>

        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-cyan-800 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-950 dark:border-cyan-900">
            <div class="flex h-full flex-col p-6">
                
                <div class="mb-6 rounded-lg border border-cyan-700 bg-gray-800/70 p-6 shadow-xl">
                    <h2 class="mb-4 text-lg font-semibold text-cyan-400">Add New Game</h2>
                    
                    <form action="{{ route('games.store') }}" method="POST" class="grid gap-4 md:grid-cols-2" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Game Name --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Game Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter game name" required 
                                class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                            @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Genre --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Genre
                                <select id="genre_id" name="genre_id" required
                                    class="w-full rounded-lg mt-2 border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                                    <option value="">Select a genre</option>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('genre_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Release Year --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Release Year</label>
                            {{-- NOTE: Changed type to 'number' and step for year input --}}
                            <input type="number" name="release_year" value="{{ old('release_year') }}" placeholder="Enter release year (YYYY)" required min="1970" max="{{ date('Y') }}"
                                class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                            @error('release_year')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Developer --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Developer</label>
                            <input type="text" name="developer" value="{{ old('developer') }}" placeholder="Enter game developer" 
                                class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                            @error('developer')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Publisher --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Publisher</label>
                            <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="Enter game publisher" 
                                class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                            @error('publisher')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ratings (Out of 5) --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-cyan-300">Rating (0.0 - 5.0)</label>
                            <input type="number" name="rating" value="{{ old('rating') }}" placeholder="Enter rating"
                                   step="0.1" min="0" max="5"
                                   class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50">
                            @error('rating')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="rounded-lg bg-cyan-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/40">
                                Add Game
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-auto">
                    <h2 class="mb-4 text-lg font-semibold text-cyan-400">Game List</h2>
                    <div class="overflow-x-auto rounded-lg border border-cyan-700">
                        <table class="w-full min-w-full text-left">
                            <thead>
                                <tr class="border-b border-cyan-700 bg-gray-800/70">
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">#</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Game Name</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Genre</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Release Year</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Developer</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Publisher</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Rating</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-700">
                                @forelse($games as $game) {{-- Renamed $movies to $game for semantic clarity, assuming the variable name wasn't changed in the controller --}}
                                    <tr class="transition-colors hover:bg-gray-800/50" id="game-row-{{ $game->id }}">
                                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-white">
                                            <span class="game-name-display">{{ $game->name }}</span> {{-- NOTE: Kept $game->title assuming 'title' holds the game name from DB --}}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-cyan-400">
                                            {{ $game->genre ? $game->genre->name : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-white">
                                            <span class="game-year-display">{{ $game->release_year }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-white">
                                            <span class="game-developer-display">{{ $game->developer }}</span> {{-- NOTE: Using a hypothetical 'developer' column --}}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-white">
                                            <span class="game-publisher-display">{{ $game->publisher }}</span> {{-- NOTE: Using a hypothetical 'publisher' column --}}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-yellow-400">
                                            <span class="game-ratings-display">{{ $game->rating ?? 'N/A' }}</span> {{-- NOTE: Using a hypothetical 'ratings' column --}}
                                        </td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                                            <button onclick="editGame(
                                                '{{ $game->id }}',
                                                '{{ addslashes($game->name) }}', 
                                                '{{ $game->genre_id }}',
                                                '{{ $game->release_year }}',
                                                '{{ addslashes($game->developer) }}',
                                                '{{ addslashes($game->publisher) }}',
                                                '{{ $game->rating }}'
                                            );" class="text-cyan-400 transition-colors hover:text-cyan-300">
                                                Edit
                                            </button>
                                            <span class="mx-1 text-cyan-700">|</span>
                                            <form action="{{ route('games.destroy', $game->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to move this game to trash?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn text-red-500 transition-colors hover:text-red-400">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-sm text-cyan-300">
                                            No games found. Add your first game above!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div id="editGameModal" class="fixed inset-0 hidden items-center justify-center bg-black/70 z-[9999]">
    <div class="w-full max-w-2xl rounded-xl border border-cyan-700 bg-gray-800 p-6 shadow-2xl">
        <h2 class="mb-4 text-lg font-semibold text-cyan-400">Edit Game</h2>
        <form id="editGameForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid gap-4 md:grid-cols-2">
                {{-- Game Name --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Game Name</label>
                    <input type="text" id="edit_game_name" name="name"
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                </div>
                {{-- Genre --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Genre</label>
                    <select id="edit_genre_select" name="genre_id"
                        class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                        <option value="">Select a genre</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Release Year --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Release Year</label>
                    <input type="number" id="edit_release_year" name="release_year" min="1970" max="{{ date('Y') }}"
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                </div>
                {{-- Developer --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Developer</label>
                    <input type="text" id="edit_developer" name="developer"
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                </div>
                {{-- Publisher --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Publisher</label>
                    <input type="text" id="edit_publisher" name="publisher"
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                </div>
                {{-- Rating --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Rating (0.0 - 5.0)</label>
                    <input type="number" id="edit_ratings" name="rating" step="0.1" min="0.0" max="5.0"
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="rounded-lg border border-cyan-600 px-4 py-2 text-sm font-medium text-cyan-300 hover:bg-gray-700">Cancel</button>
                <button type="submit" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">Update Game</button>
            </div>
        </form>
    </div>
</div>

{{-- NOTE: Updated Javascript function name and parameters --}}
<script>
    function editGame(id, name, genre_id, release_year, developer, publisher, rating) {
        document.getElementById('editGameModal').classList.remove('hidden');
        document.getElementById('editGameModal').classList.add('flex');
        document.getElementById('editGameForm').action = `/games/${id}`; // NOTE: Updated route path

        document.getElementById('edit_game_name').value = name;
        document.getElementById('edit_genre_select').value = genre_id;
        document.getElementById('edit_release_year').value = release_year;
        document.getElementById('edit_developer').value = developer;
        document.getElementById('edit_publisher').value = publisher;
        document.getElementById('edit_ratings').value = rating;
    }

    function closeEditModal() {
        document.getElementById('editGameModal').classList.add('hidden');
        document.getElementById('editGameModal').classList.remove('flex');
        document.getElementById('editGameForm').reset();
    }
</script>
</x-layouts.app>