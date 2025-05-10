<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    // autorise l'injection de ces champs par create()/update()
    protected $fillable = [
        'mal_id',
        'titre',
        'synopsis',
        'image_url',
        'auteur_id',
    ];

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_anime')
            ->withTimestamps();
    }

    public function episodesVu()
    {
        return $this->belongsToMany(Episode::class, 'user_episode')
            ->withTimestamps();
    }
}
