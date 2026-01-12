<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;

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

        $games = $query->latest()->get();
        $genres = Genre::all();
        $topGame = Game::orderBy('rating', 'desc')->first();

        return view('games', compact('games', 'genres', 'topGame'));
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
        return redirect()->route('dashboard')->with('success', 'Game added successfully');
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
        $game->delete();
        return redirect()->route('games.trash')->with('success', 'Game moved to trash successfully');
    }

    public function trash()
    {
        $games = Game::onlyTrashed()->with('genre')->latest('deleted_at')->get();
        $genres = Genre::all();

        return view('trash', compact('games', 'genres'));
    }

    public function restore($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->restore();

        return redirect()->route('games.index')->with('success', 'Game restored successfully');
    }

    public function forceDelete($id)
    {
        $game = Game::withTrashed()->findOrFail($id);

        if ($game->photo) {
            Storage::disk('public')->delete($game->photo);
        }

        $game->forceDelete();

        return redirect()->route('games.trash')->with('success', 'Game permanently deleted successfully');
    }

    public function export(Request $request)
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

        $filename = 'games_export_' . date('Y-m-d_His') . '.pdf';

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Games Export</title>
            <style>
                body {
                    font-family: "Helvetica", Arial, sans-serif;
                    background: #f3f4f6;
                    margin: 0;
                    padding: 30px;
                    color: #111827;
                }

                .container {
                    max-width: 1100px;
                    margin: auto;
                    background: #ffffff;
                    padding: 32px;
                    border-radius: 8px;
                }

                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }

                .header h1 {
                    margin: 0;
                    font-size: 26px;
                    letter-spacing: 0.5px;
                }

                .header p {
                    margin-top: 8px;
                    font-size: 14px;
                    color: #6b7280;
                }

                .divider {
                    height: 2px;
                    background: #e5e7eb;
                    margin: 25px 0;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                }

                th {
                    background: #0f172a;
                    color: #ffffff;
                    padding: 12px 10px;
                    text-align: left;
                }

                td {
                    padding: 10px;
                    border-bottom: 1px solid #e5e7eb;
                    vertical-align: top;
                }

                tr:nth-child(even) {
                    background: #f9fafb;
                }

                .badge {
                    display: inline-block;
                    padding: 4px 8px;
                    font-size: 12px;
                    border-radius: 12px;
                    background: #e0f2fe;
                    color: #0369a1;
                    font-weight: 600;
                }

                .rating {
                    font-weight: bold;
                    color: #f59e0b;
                }

                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 13px;
                    color: #6b7280;
                }

                @media print {
                    body {
                        background: white;
                        padding: 0;
                    }
                    .container {
                        border-radius: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">

                <div class="header">
                    <h1>Games Report</h1>
                    <p>
                        Exported on ' . date('F d, Y \\a\\t h:i A') . '<br>
                        Total Records: ' . $games->count() . '
                    </p>
                </div>

                <div class="divider"></div>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Game Name</th>
                            <th>Genre</th>
                            <th>Release Year</th>
                            <th>Developer</th>
                            <th>Publisher</th>
                            <th>Rating</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>';
                $number = 1;
                foreach ($games as $game) {
                    $html .= '<tr>
                    <td>' . $number++ . '</td>
                    <td>' . htmlspecialchars($game->name) . '</td>
                    <td>
                        <span class="badge">' . htmlspecialchars($game->genre ? $game->genre->name : 'No Genre') . '</span>
                    </td>
                    <td>' . htmlspecialchars($game->release_year) . '</td>
                    <td>' . htmlspecialchars($game->developer ?? '-') . '</td>
                    <td>' . htmlspecialchars($game->publisher ?? '-') . '</td>
                    <td class="rating">' . htmlspecialchars($game->rating ?? 'N/A') . '</td>
                    <td>' . $game->created_at->format('Y-m-d H:i:s') . '</td>
                </tr>';
                }

                $html .= '</tbody>
                </table>

                <div class="footer">
                    Total Games: ' . $games->count() . '<br/>
                    © ' . date('Y') . ' GameVault. All rights reserved.
                </div>
            </div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream($filename, ['Attachment' => true]);

    }
}
