<x-layouts.app :title="__('Game Genres')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="rounded-lg bg-green-900/30 p-4 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Main Content Container (Gaming Theme) --}}
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-cyan-800 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-950">
            <div class="flex h-full flex-col p-6">

                {{-- Add New Genre Form Section --}}
                <div class="mb-6 rounded-lg border border-cyan-700 bg-gray-800/70 p-6 shadow-xl">
                    <h2 class="mb-4 text-lg font-semibold text-cyan-400">Add New Genre</h2>

                    <form action="{{ route('genres.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-3">
                            {{-- Genre Name Input --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-cyan-300">Genre Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                        placeholder="Enter genre name (e.g., RPG, Strategy)" required
                                        class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description Textarea --}}
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-cyan-300">Description</label>
                                <textarea name="description" rows="1" placeholder="Describe the genre"
                                            class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-gray-50 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-lg bg-cyan-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/40">
                                Add Genre
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-auto">
                    <h2 class="mb-4 text-lg font-semibold text-cyan-400">Genre List</h2>
                    <div class="overflow-x-auto rounded-lg border border-cyan-700">
                        <table class="w-full min-w-full text-left">
                            <thead>
                                <tr class="border-b border-cyan-700 bg-gray-800/70">
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-cyan-200">#</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Genre Name</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-cyan-200">Description</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-cyan-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-700">
                                @forelse($genres as $genre)
                                    <tr class="transition-colors hover:bg-gray-800/50" id="genre-row-{{ $genre->id }}">
                                        <td class="px-4 py-3 text-center text-sm text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-white">
                                            <span class="genre-name-display">{{ $genre->name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-400">
                                            <span class="genre-description-display">{{ Str::limit($genre->description, 50) ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm whitespace-nowrap">
                                            <button onclick="editGenre({{ $genre->id }}, '{{ addslashes($genre->name) }}', '{{ addslashes($genre->description) }}')"
                                                    class="text-cyan-400 transition-colors hover:text-cyan-300">
                                                Edit
                                            </button>
                                            <span class="mx-1 text-cyan-700">|</span>
                                            <form action="{{ route('genres.destroy', $genre->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this genre? This action cannot be undone.')">
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
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-cyan-300">
                                            No genres found. Add your first genre above!
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

{{-- Edit Genre Modal (Updated Styling) --}}
<div id="editGenreModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="w-full max-w-2xl rounded-xl border border-cyan-700 bg-gray-800 p-6 shadow-2xl">
        <h2 class="mb-4 text-lg font-semibold text-cyan-400">Edit Genre</h2>

        <form id="editGenreForm" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                {{-- Genre Name Input --}}
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Genre Name</label>
                    <input type="text" id="edit_genre_name" name="name" required
                           class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                </div>

                {{-- Description Textarea --}}
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-cyan-300">Description</label>
                    <textarea id="edit_description" name="description" rows="3"
                              class="w-full rounded-lg border border-cyan-600 bg-gray-900 px-4 py-2 text-sm text-white focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="rounded-lg border border-cyan-600 px-4 py-2 text-sm font-medium text-cyan-300 hover:bg-gray-700">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">
                    Update Genre
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript remains the same, but the modal ID and class names are updated --}}
<script>
    function editGenre(id, name, description) {
        document.getElementById('editGenreModal').classList.remove('hidden');
        document.getElementById('editGenreModal').classList.add('flex');
        document.getElementById('editGenreForm').action = `/genres/${id}`;

        document.getElementById('edit_genre_name').value = name;
        document.getElementById('edit_description').value = description || '';
    }

    function closeEditModal() {
        document.getElementById('editGenreModal').classList.add('hidden');
        document.getElementById('editGenreModal').classList.remove('flex');
        document.getElementById('editGenreForm').reset();
    }
</script>
</x-layouts.app>