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
        // 1) on ne valide que le mal_id
        $validated = $request->validate([
            'mal_id' => 'required|integer|unique:animes,mal_id',
        ]);

        // 2) on délègue tout à AnimeService (attachToUser = false)
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
    public function show(Anime $anime)
    {
        return response()->json($anime->load('episodes'));
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
    public function progression(Request $request, Anime $anime)
    {
        $total = $anime->episodes()->count();
        $seen  = $request->user()
            ->episodesVu()
            ->where('anime_id', $anime->id)
            ->count();

        return response()->json([
            'anime_id'       => $anime->id,
            'total_episodes' => $total,
            'seen_episodes'  => $seen,
        ]);
    }
}
