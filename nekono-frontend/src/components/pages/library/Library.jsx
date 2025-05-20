// src/components/pages/library/Library.jsx
import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import useLibrary from "../../../hooks/useLibrary.jsx";
import "./library.css";

export function Library() {
    const navigate = useNavigate();
    const { library, loading, error, removeAnime, refresh } = useLibrary();

    useEffect(() => {
        refresh();
    }, [refresh]);

    if (loading) return <p>Chargement de votre bibliothèque…</p>;
    if (error)   return <p className="error">{error}</p>;

    return (
        <>
            {library.length === 0 ? (
                <p>Votre bibliothèque est vide.</p>
            ) : (
                <div className="cards">
                    {library.map(anime => {
                        const { seen_episodes, total_episodes } = anime.progress;
                        const pct = total_episodes
                            ? Math.round((seen_episodes / total_episodes) * 100)
                            : 0;

                        return (
                            <div
                                key={anime.mal_id}
                                className="card"
                                onDoubleClick={() => navigate(`/anime/${anime.mal_id}`)}
                            >
                                <img
                                    className="card__img"
                                    src={anime.image_url}
                                    alt={anime.titre}
                                />
                                <div className="card__body">
                                    <h3 className="card__title">{anime.titre}</h3>

                                    {/* barre de progression */}
                                    <div className="progress-container">
                                        <div
                                            className="progress-bar"

                                        />
                                    </div>
                                    <small>{seen_episodes} / {total_episodes} épisodes vus</small>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </>
    );
}
