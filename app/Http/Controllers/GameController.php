<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::with('genre');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('developer', 'like', "%{$searchTerm}%")
                    ->orWhere('publisher', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('genre_filter') && $request->genre_filter != '') {
            $query->where('genre_id', $request->genre_filter);
        }

        $games = Game::latest()->get();
        $genres = Genre::all();
        $topGame = Game::orderBy('rating', 'desc')->first();

        return view('dashboard', compact('games', 'genres', 'topGame'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'release_year' => 'required|digits:4|integer',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'rating' => 'required|numeric|between:1,5',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('game-photos', 'public');
            $validated['photo'] = $photoPath;
        }

        Game::create($validated);
        return redirect()->back()->with('success', 'Game added successfully');
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'release_year' => 'required|digits:4|integer',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'rating' => 'required|numeric|between:1,5',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($game->photo) {
                Storage::disk('public')->delete($game->photo);
            }

            $photoPath = $request->file('photo')->store('game-photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $game->update($validated);
        return redirect()->back()->with('success', 'Game updated successfully');
    }

    public function destroy(Game $game)
    {
        if ($game->photo) {
            Storage::disk('public')->delete($game->photo);
        }

        $game->delete();
        return redirect()->back()->with('success', 'Game deleted successfully');
    }
}
