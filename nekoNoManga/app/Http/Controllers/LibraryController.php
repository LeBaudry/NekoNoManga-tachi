<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Services\AnimeService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    protected AnimeService $animeService;

    public function __construct(AnimeService $animeService)
    {
        $this->animeService = $animeService;
    }

    /**
     * Liste tous les animes de la bibliothèque de l'utilisateur.
     */
    public function index(Request $request)
    {
        $animes = $request->user()
            ->animes()                  // ou library() selon ta relation
            ->with('episodes')
            ->get();

        return response()->json($animes);
    }

    /**
     * Ajoute un anime (et ses épisodes) à la bibliothèque de l'utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mal_id' => 'required|integer',
        ]);

        // 1) synchronise l'anime + épisodes en base, et l'attache à l'user
        $anime = $this->animeService->syncFromJikan(
            $validated['mal_id'],
            true  // true = on attache aussi à l'utilisateur
        );

        return response()->json([
            'message' => 'Ajouté à votre bibliothèque',
            'anime'   => $anime->load('episodes'),
        ], 201);
    }

    /**
     * Supprime un anime de la bibliothèque de l'utilisateur.
     */
    public function destroy(Request $request, Anime $anime)
    {
        $request->user()
            ->animes()       // ou library()
            ->detach($anime->id);

        return response()->json(['message' => 'Supprimé de votre bibliothèque']);
    }
}
