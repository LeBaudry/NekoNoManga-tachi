// src/hooks/useLibrary.jsx
import { useState, useEffect, useCallback } from "react";
import axios from "axios";



export default function useLibrary() {
    const [library, setLibrary] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // auth header si besoin
    const token = localStorage.getItem("token");
    const headers = token ? { Authorization: `Bearer ${token}` } : {};

    const fetchLibrary = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const res = await axios.get(`http://localhost:8000/api/library/animes`, { headers });
            setLibrary(res.data);
        } catch (err) {
            console.error(err);
            setError("Impossible de récupérer votre bibliothèque");
        } finally {
            setLoading(false);
        }
    }, []);

    const addAnime = async (mal_id) => {
        setLoading(true);
        try {
            await axios.post(`http://localhost:8000/api/library/animes`, { mal_id }, { headers });
            await fetchLibrary();
        } catch (err) {
            console.error(err);
            setError("Échec de l’ajout à la bibliothèque");
        } finally {
            setLoading(false);
        }
    };

    const removeAnime = async (mal_id) => {
        setLoading(true);
        try {
            // 1) on retrouve la ligne dans la bibliothèque
            const entry = library.find(a => a.mal_id === mal_id);
            if (!entry) throw new Error("Anime non trouvé en base");

            // 2) on récupère l'ID réel de la table animes
            const animeId = entry.mal_id;

            // 3) on appelle la route DELETE /api/library/animes/{animeId}
            await axios.delete(`http://localhost:8000/api/library/animes/${animeId}`, { headers });
            await fetchLibrary();
        } catch (err) {
            console.error(err);
            setError("Échec de la suppression de votre bibliothèque");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLibrary();
    }, [fetchLibrary]);

    return {
        library,
        loading,
        error,
        addAnime,
        removeAnime,
        refresh: fetchLibrary,
    };
}
