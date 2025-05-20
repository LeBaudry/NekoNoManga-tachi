<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'anime_id',
        'numero',    // => correspond à l'épisode n° dans votre table
        'titre',
        'synopsis',
        'mal_id',
    ];

    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }

    public function vuPar()
    {
        return $this->belongsToMany(User::class, 'user_episode')->withPivot('vu_le');
    }

}
