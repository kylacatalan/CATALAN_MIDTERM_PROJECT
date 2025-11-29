<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $games = Game::latest()->get();
        $genres = Genre::all();

        return view('genres', compact('games', 'genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Genre::create($validated);
        return redirect()->back()->with('success', 'Genre created successfully');
    }

    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $genre->update($validated);
        return redirect()->back()->with('success', 'Genre updated successufully');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->back()->with('success', 'Genre deleted successfully');
    }
}
