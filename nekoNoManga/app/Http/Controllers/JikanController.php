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
        $page  = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $data  = $this->jikan->listAnime($page, $limit);
        return response()->json([
            'data'       => $data,
            'page'       => (int)$page,
            'per_page'   => (int)$limit,
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
