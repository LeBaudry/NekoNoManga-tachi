// src/components/pages/library/Library.jsx
import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import useLibrary from "../../../hooks/useLibrary.jsx";
import "./library.css";

export function Library() {
    const navigate = useNavigate();
    const {
        library,
        loading,
        error,
        removeAnime,
        refresh
    } = useLibrary();

    // Au montage on charge la bibliothèque
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
                    {library.map(anime => (
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
                                <button
                                    className="remove-button"
                                    onClick={e => {
                                        e.stopPropagation();
                                        removeAnime(anime.mal_id);
                                    }}
                                >
                                    Retirer
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </>
    );
}
