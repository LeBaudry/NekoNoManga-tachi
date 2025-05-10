<?php
namespace App\Services;

use App\Models\Anime;
use App\Services\JikanService;
use Illuminate\Support\Facades\Auth;

class AnimeService
{
    protected JikanService $jikan;

    public function __construct(JikanService $jikan)
    {
        $this->jikan = $jikan;

    }

    /**
     * Crée ou met à jour un anime + ses épisodes en base,
     * puis (optionnel) l'attache à l'utilisateur.
     */
    public function syncFromJikan(int $malId, bool $attachToUser = false): Anime
    {
        // 1) Récupère les données de l’anime
        $data = $this->jikan->getAnime($malId);

        // 2) Crée ou met à jour l’anime en base
        $anime = Anime::updateOrCreate(
            ['mal_id' => $malId],
            [
                'titre'     => $data['title'],
                'synopsis'  => $data['synopsis'] ?? null,
                'image_url' => $data['images']['jpg']['image_url'] ?? null,
            ]
        );

        // 3) Récupère tous les épisodes et sync
        $episodes = $this->jikan->getEpisodes($malId);
        foreach ($episodes as $ep) {
            $anime->episodes()->updateOrCreate(
                ['numero' => $ep['mal_id']],
                [
                    'mal_id'   => $ep['mal_id'],
                    'titre'    => $ep['title'],
                    'synopsis' => $ep['synopsis'] ?? null,
                    'air_date' => $ep['aired']['from'] ?? null,
                ]
            );
        }

        // 4) Si demandé, attache à la bibliothèque de l’utilisateur
        if ($attachToUser) {
            Auth::user()->animes()->syncWithoutDetaching($anime->id);
        }

        return $anime;
    }
}
