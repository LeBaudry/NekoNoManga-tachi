<?php

namespace App\Services;

use GuzzleHttp\Client;

class JikanService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.jikan.moe/v4/',
            'timeout'  => 5.0,
            // si besoin, désactivez la vérif. SSL locale :
            'verify'   => false,
        ]);
    }

    public function listAnime(int $page = 1, int $limit = 25): array
    {
        $res = $this->client->get('anime', [
            'query' => ['page' => $page, 'limit' => $limit]
        ]);
        return json_decode($res->getBody(), true)['data'];
    }

    /**
     * Recherche d'animes par mot-clé
     */
    public function searchAnime(string $query, int $limit = 10): array
    {
        $res = $this->client->get('anime', [
            'query' => ['q' => $query, 'limit' => $limit]
        ]);

        return json_decode($res->getBody(), true)['data'];
    }

    /**
     * Récupère les détails d'un anime (endpoint /anime/{id}/full)
     */
    public function getAnime(int $malId): array
    {
        $res = $this->client->get("anime/{$malId}/full");
        return json_decode($res->getBody(), true)['data'];
    }

    /**
     * Récupère la liste des épisodes d'un anime
     */
    public function getEpisodes(int $malId): array
    {
        $res = $this->client->get("anime/{$malId}/episodes");
        return json_decode($res->getBody(), true)['data'];
    }
}
