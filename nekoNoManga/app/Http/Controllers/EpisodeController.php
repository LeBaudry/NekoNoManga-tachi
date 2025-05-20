<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anime;
use Illuminate\Http\Request;
use App\Models\Episode;

class EpisodeController extends Controller
{
    public function toggleVu(Request $request, $animeId, $numero)
    {
        $anime = Anime::findOrFail($animeId);

        // On récupère l’épisode correspondant au numero et à l’anime
        $episode = $anime->episodes()->where('numero', $numero)->first();

        if (! $episode) {
            return response()->json(['message' => 'Épisode non trouvé pour cet anime'], 404);
        }

        // 2) on attache/détache
        $user = $request->user();
        if ($user->episodesVu()->wherePivot('episode_id', $episode->id)->exists()) {
            $user->episodesVu()->detach($episode->id);
            $action = 'détaché';
        } else {
            $user->episodesVu()->attach($episode->id, ['vu_le' => now()]);
            $action = 'attaché';
        }

        return response()->json([
            'message'    => "Épisode {$action} avec succès.",
            'episode_id' => $episode->id,
            'anime_id'   => $anime->id,
            'watched'    => $action === 'attaché',
        ]);
    }
}
