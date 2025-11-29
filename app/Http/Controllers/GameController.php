<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Genre;

class GameController extends Controller
{
    public function index()
    {
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
            'rating' => 'required|numeric|between:1,5'
        ]);

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
            'rating' => 'required|numeric|between:1,5'
        ]);

        $game->update($validated);
        return redirect()->back()->with('success', 'Game updated successfully');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->back()->with('success', 'Game deleted successfully');
    }
}
