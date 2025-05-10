<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class, 'user_anime')
            ->withTimestamps();
    }

    public function episodesVu()
    {
        return $this->belongsToMany(
            Episode::class,
            'user_episode',    // <-- nom exact de votre table pivot
            'user_id',         // clé étrangère de ce modèle dans la pivot
            'episode_id'       // clé étrangère de l’autre modèle dans la pivot
        )
            ->withPivot('vu_le');

    }

}
