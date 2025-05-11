// hooks/useAnimeDetail.jsx
import { useState, useEffect, useCallback } from "react";
import axios from "axios";

const useAnimeDetail = (animeId) => {
    const [anime, setAnime]       = useState({ episodes: [] });
    const [progress, setProgress] = useState({ seen_episodes: 0, total_episodes: 0 });
    const [loading, setLoading]   = useState(true);
    const [error, setError]       = useState(null);

    // récupère le token
    const token = localStorage.getItem("token");
    const authHeader = token ? { Authorization: `Bearer ${token}` } : {};

    const getDetail = useCallback(async () => {
        setLoading(true);
        setError(null);

        try {
            const [aRes, pRes] = await Promise.all([
                // → DETAIL via MAL ID
                axios.get(`http://localhost:8000/api/animes/jikan/${animeId}`, {
                    headers: authHeader
                }),
                // → Progression (ID interne) si déjà en base
                axios.get(`http://localhost:8000/api/animes/${animeId}/progression`, {
                    headers: authHeader
                })
            ]);

            setAnime({
                ...aRes.data,
                episodes: Array.isArray(aRes.data.episodes)
                    ? aRes.data.episodes
                    : []
            });
            setProgress(pRes.data || { seen_episodes: 0, total_episodes: 0 });
        } catch {
            setError("Impossible de charger l'anime");
        } finally {
            setLoading(false);
        }
    }, [animeId]);


    useEffect(() => {
        getDetail();
    }, [getDetail]);

    const toggleVu = async (episodeId) => {
        try {
            // 1) On s'assure que l'anime est bien dans la bibliothèque :
            await axios.post(
                "http://localhost:8000/api/library/animes",
                { mal_id: anime.mal_id },  // on passe le mal_id côté back
                { headers: authHeader }
            );

            // 2) Puis on toggle l'épisode
            await axios.post(
                `http://localhost:8000/api/animes/${animeId}/episodes/${episodeId}/toggle`,
                {},
                { headers: authHeader }
            );

            // 3) On recharge les données
            await getDetail();
        } catch (err) {
            console.error("Toggle error", err);
            setError("Impossible de marquer l'épisode");
        }
    };
    return { anime, progress, loading, error, toggleVu };
};

export default useAnimeDetail;
