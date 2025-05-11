<?php

namespace App\Http\Controllers;

use App\Services\JikanService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class JikanController extends Controller
{
    public function __construct(protected JikanService $jikan) {}

    public function list(Request $request)
    {
        $page  = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 25);

        $result = $this->jikan->listAnime($page, $limit);

        return response()->json([
            'data'       => $result['data'],
            'pagination' => [
                'page'              => $page,
                'per_page'          => $result['pagination']['per_page'],
                'total'             => $result['pagination']['total'],
                'count'             => $result['pagination']['count'],
                'last_visible_page' => $result['pagination']['last_visible_page'],
                'has_next_page'     => $result['pagination']['has_next_page'],
            ],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string']);
        return response()->json(
            $this->jikan->searchAnime($request->input('q'))
        );
    }

    public function episodes(int $malId)
    {
        return response()->json(
            $this->jikan->getAnimeEpisodes($malId)
        );
    }
}
