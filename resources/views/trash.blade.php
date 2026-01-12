<x-layouts.app :title="__('Game Trash')">
    <div class="space-y-6">

        {{-- Success Message --}}
        @if(session('success'))
            <div class="rounded-lg bg-green-900/30 p-4 text-sm text-green-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-cyan-400">Game Trash</h1>
                <p class="mt-1 text-sm text-gray-400">
                    Restore or permanently delete games
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">
                Back to Dashboard
            </a>
        </div>

        {{-- Summary Card --}}
        <div class="rounded-xl border border-cyan-700 bg-gray-900 p-5 shadow-lg shadow-cyan-900/20">
            <p class="text-sm font-medium text-cyan-400">Games in Trash</p>
            <p class="mt-1 text-3xl font-bold text-white">{{ $games->count() }}</p>
        </div>

        {{-- Table Container --}}
        <div class="relative overflow-hidden rounded-xl border border-cyan-800 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-950">
            <div class="p-6">

                <h2 class="mb-4 text-lg font-semibold text-cyan-400">Deleted Games</h2>

                @if($games->isEmpty())
                    <div class="flex items-center justify-center rounded-lg border border-dashed border-cyan-700 p-12">
                        <div class="text-center">
                            <h3 class="text-sm font-medium text-cyan-300">Trash is empty</h3>
                            <p class="mt-1 text-sm text-gray-400">No deleted games found.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-cyan-700">
                        <table class="w-full text-left">
                            <thead class="bg-gray-800/70 border-b border-cyan-700">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Photo</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Game</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Genre</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Year</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Rating</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200">Deleted At</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-cyan-200 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-cyan-700">
                                @foreach($games as $game)
                                    <tr class="transition-colors hover:bg-gray-800/50">
                                        <td class="px-4 py-3">
                                            @if($game->photo)
                                                <img
                                                    src="{{ Storage::url($game->photo) }}"
                                                    class="h-10 w-10 rounded-full object-cover ring-2 ring-cyan-500/40"
                                                >
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-900/30 text-sm font-semibold text-cyan-300">
                                                    {{ strtoupper(substr($game->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-sm font-medium text-white">
                                            {{ $game->name }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-cyan-400">
                                            {{ $game->genre?->name ?? 'N/A' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-white">
                                            {{ $game->release_year }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-yellow-400">
                                            {{ $game->rating ?? 'N/A' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-400">
                                            {{ $game->deleted_at->format('M d, Y') }}
                                            <div class="text-xs">
                                                {{ $game->deleted_at->format('h:i A') }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-2">
                                                {{-- Restore --}}
                                                <form method="POST" action="{{ route('games.restore', $game->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="return confirm('Restore this game?')"
                                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                                        Restore
                                                    </button>
                                                </form>

                                                {{-- Delete Forever --}}
                                                <form method="POST" action="{{ route('games.force-delete', $game->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Permanently delete this game? This cannot be undone!')"
                                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                                                        Delete Forever
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-layouts.app>
