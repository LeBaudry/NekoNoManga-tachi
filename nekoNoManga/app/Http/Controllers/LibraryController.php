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

        $user = $request->user();

        // Vérifie si l'anime est déjà en base
        $anime = Anime::where('mal_id', $validated['mal_id'])->first();

        if (!$anime) {
            // Pas trouvé en DB → on appelle Jikan pour créer + stocker
            $anime = $this->animeService->syncFromJikan($validated['mal_id'], false); // false = pas encore attaché
        }

        // Vérifie s'il est déjà attaché à l'utilisateur
        $alreadyInLibrary = $user->animes()->where('anime_id', $anime->id)->exists();

        if (!$alreadyInLibrary) {
            $user->animes()->attach($anime->id);
        }

        return response()->json([
            'message' => $alreadyInLibrary ? 'Déjà présent dans votre bibliothèque' : 'Ajouté à votre bibliothèque',
            'anime'   => $anime->load('episodes'),
        ], $alreadyInLibrary ? 200 : 201);
    }


    /**
     * Supprime un anime de la bibliothèque de l'utilisateur.
     */
    public function destroy(Request $request, $id)
    {
        $anime = Anime::find($id);

        if (!$anime) {
            return response()->json(['message' => 'Anime introuvable'], 404);
        }

        $request->user()->animes()->detach($anime->id);

        return response()->json(['message' => 'Supprimé de votre bibliothèque']);
    }
}
