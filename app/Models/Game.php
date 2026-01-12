<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'genre_id',
        'release_year',
        'developer',
        'publisher',
        'rating',
        'photo',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
