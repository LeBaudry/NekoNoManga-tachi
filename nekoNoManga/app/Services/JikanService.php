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
            'query' => ['page' => $page, 'limit' => $limit],
        ]);
        $json = json_decode($res->getBody(), true);

        // Jikan v4 : pagination.items contient count, total, per_page
        $items = $json['pagination']['items'] ?? [];

        return [
            'data'       => $json['data'] ?? [],
            'pagination' => [
                'total'    => $items['total']    ?? null,
                'count'    => $items['count']    ?? null,
                'per_page' => $items['per_page'] ?? null,
                'last_visible_page' => $json['pagination']['last_visible_page'] ?? null,
                'has_next_page'     => $json['pagination']['has_next_page']     ?? null,
            ],
        ];
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
        $all   = [];
        $page  = 1;
        $limit = 100;

        do {
            $res = $this->client->get("anime/{$malId}/episodes", [
                'query' => ['page' => $page, 'limit' => $limit],
            ]);

            $json    = json_decode($res->getBody(), true);
            $data    = $json['data'] ?? [];
            $all     = array_merge($all, $data);

            // Jikan v4 renvoie un bloc pagination.has_next_page
            $hasNext = $json['pagination']['has_next_page'] ?? false;
            $page++;
            sleep(10);
        } while ($hasNext);

        return $all;
    }
}
