<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Genre;

class DashboardController extends Controller
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

        $games = $query->latest()->get();
        $genres = Genre::all();

        return view('dashboard', compact('games', 'genres'));
    }
}
