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
            // 1) On récupère l'anime via MAL ID (public)
            const aRes = await axios.get(`http://localhost:8000/api/animes/jikan/${animeId}`, {
                headers: authHeader
            });

            const loadedAnime = {
                ...aRes.data,
                episodes: Array.isArray(aRes.data.episodes) ? aRes.data.episodes : []
            };
            setAnime(loadedAnime);

            // 2) Si l'anime existe en base, on récupère la progression (private)
            if (loadedAnime.id) {
                const pRes = await axios.get(
                    `http://localhost:8000/api/animes/${loadedAnime.id}/progression`,
                    { headers: authHeader }
                );
                setProgress(pRes.data || { seen_episodes: 0, total_episodes: 0 });
            } else {
                setProgress({ seen_episodes: 0, total_episodes: 0 });
            }

        } catch (err) {
            console.error(err);
            setError("Impossible de charger l'anime");
        } finally {
            setLoading(false);
        }
    }, [animeId]);


    useEffect(() => {
        getDetail();
    }, [getDetail]);

    const toggleVu = async (episodeNumero) => {
        try {
            // 1) On fait la requête de toggle sur le backend
            await axios.post(
                `http://localhost:8000/api/animes/${anime.id}/episodes/${episodeNumero}/toggle`,
                {},
                { headers: authHeader }
            );

            // 2) Mise à jour locale optimiste
            setAnime(prev => {
                const updatedEpisodes = prev.episodes.map(ep => {
                    if (ep.numero === episodeNumero) {
                        const wasWatched = !!ep.pivot?.watched_at;

                        return {
                            ...ep,
                            pivot: wasWatched
                                ? undefined
                                : { watched_at: new Date().toISOString() }
                        };
                    }
                    return ep;
                });

                // recalcul du progrès
                const total = updatedEpisodes.length;
                const seen = updatedEpisodes.filter(ep => ep.pivot?.watched_at).length;

                setProgress({ total_episodes: total, seen_episodes: seen });

                return { ...prev, episodes: updatedEpisodes };
            });
        } catch (err) {
            console.error("Toggle error", err);
            setError("Impossible de marquer l'épisode");
        }
    };
    return { anime, progress, loading, error, toggleVu };
};

export default useAnimeDetail;
