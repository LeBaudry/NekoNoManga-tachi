<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;
use App\Services\JikanService;
use App\Services\AnimeService;


class AnimeController extends Controller
{
    protected $jikan;
    protected $animeService;

    public function __construct(JikanService $jikan,AnimeService $animeService)
    {
        $this->jikan = $jikan;
        $this->animeService = $animeService;
    }

    /**
     * GET /animes
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $animes  = Anime::with('episodes')
            ->orderBy('titre')
            ->paginate($perPage);

        return response()->json($animes);
    }

    /**
     * POST /animes
     * Crée l’anime d’après le mal_id et hydrate les épisodes depuis Jikan.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'mal_id' => 'required|integer|unique:animes,mal_id',
        ]);


        $anime = $this->animeService->syncFromJikan(
            $validated['mal_id'],
            false
        );

        // 3) on renvoie l'anime chargé avec ses épisodes
        return response()->json(
            $anime->load('episodes'),
            201
        );
    }

    /**
     * GET /animes/{anime}
     */
    public function show(Request $request, int $malId)
    {
        // On cherche d'abord en base
        $anime = Anime::with('episodes')
            ->where('mal_id', $malId)
            ->first();

        if ($anime) {
            // S’il n’a pas d’épisodes en base, on les sync
            if ($anime->getepisodes()->count() === 0) {
                $this->syncFromJikan($anime);
            }
            return response()->json($anime, 200);
        }

        // Sinon on récupère depuis Jikan **sans persister**
        $info     = $this->jikan->getAnime($malId);
        $episodes = $this->jikan->getEpisodes($malId);

        // On renvoie juste les données, sans toucher à la base
        return response()->json([
            'mal_id'    => $info['mal_id'],
            'titre'     => $info['title'],
            'synopsis'  => $info['synopsis'] ?? null,
            'image_url' => $info['images']['jpg']['image_url'] ?? null,
            'episodes'  => array_map(fn($ep) => [
                'numero'    => $ep['episode'] ?? $ep['mal_id'],
                'mal_id'    => $ep['mal_id'],
                'titre'     => $ep['title'],
                'synopsis'  => $ep['synopsis'] ?? null,
                'air_date'  => $ep['aired']['from'] ?? null,
                // pas de pivot ici, ce n'est pas en base
            ], $episodes),
        ], 200);
    }

    public function showByMalId(Request $request, int $malId)
    {
        try {
            $user = $request->user();

            $anime = Anime::firstOrNew(['mal_id' => $malId]);

            if (! $anime->exists) {
                $anime = $this->animeService->syncFromJikan($malId, false);
            } elseif ($anime->episodes()->count() === 0) {
                $this->syncFromJikan($anime);
            }

            $anime->load([
                'episodes' => function ($q) use ($user) {
                    $q->with(['vuPar' => function ($q2) use ($user) {
                        $q2->where('user_id', $user->id);
                    }]);
                }
            ]);

            $anime->episodes->transform(function ($ep) {
                $vu = $ep->vuPar->first();
                if ($vu && $vu->pivot && isset($vu->pivot->vu_le)) {
                    $ep->pivot = (object)['watched_at' => $vu->pivot->vu_le];
                } else {
                    $ep->pivot = null;
                }
                unset($ep->vuPar);
                return $ep;
            });

            return response()->json($anime, 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    protected function syncFromJikan(Anime $anime)
    {
        // 1) Récupère les données complètes d’un coup
        $info     = $this->jikan->getAnime($anime->mal_id);
        $episodes = $this->jikan->getEpisodes($anime->mal_id);

        // 2) Met à jour l’anime en base (titre, synopsis, image…)
        $anime->update([
            'titre'     => $info['title'],
            'synopsis'  => $info['synopsis'] ?? null,
            'image_url' => $info['images']['jpg']['image_url'] ?? null,
        ]);

        // 3) Sync des épisodes
        foreach ($episodes as $ep) {
            $anime->episodes()->updateOrCreate(
                ['numero' => $ep['episode'] ?? $ep['mal_id']],
                [
                    'mal_id'    => $ep['mal_id'],
                    'titre'     => $ep['title'],
                    'synopsis'  => $ep['synopsis'] ?? null,
                    'air_date'  => $ep['aired']['from'] ?? null,
                ]
            );
        }
    }

    /**
     * PUT/PATCH /animes/{anime}
     */
    public function update(Request $request, Anime $anime)
    {
        $validated = $request->validate([
            'titre'     => 'sometimes|required|string|max:255',
            'synopsis'  => 'nullable|string',
            'image_url' => 'nullable|url',
            'auteur_id' => 'nullable|exists:auteurs,id',
        ]);

        $anime->update($validated);
        return response()->json($anime->load('episodes'));
    }

    /**
     * DELETE /animes/{anime}
     */
    public function destroy(Anime $anime)
    {
        $anime->delete();
        return response()->json(['message' => 'Anime supprimé'], 200);
    }

    /**
     * GET /animes/{anime}/progression
     */
    public function progression(Request $request, int $animeId)
    {
        $anime = Anime::find($animeId);

        if (! $anime) {
            return response()->json(['message' => 'Anime non trouvé'], 404);
        }

        $total = $anime->episodes()->count();
        $seen = $request->user()
            ->episodesVu()
            ->where('anime_id', $anime->id)
            ->count();

        return response()->json([
            'anime_id' => $anime->id,
            'total_episodes' => $total,
            'seen_episodes' => $seen,
        ]);
    }
}
