// components/pages/animeDetail/AnimeDetail.jsx
import { useParams } from "react-router-dom";
import useAnimeDetail from "../../../hooks/useAnimeDetail";
import "./animeDetail.css";
import useLibrary from "../../../hooks/useLibrary.jsx";

export function AnimeDetail() {
    const { id } = useParams();
    const { anime, progress, loading, error, toggleVu } = useAnimeDetail(id);
    const { library, addAnime, removeAnime, loading: libLoading } = useLibrary();

    const entry = library.find(a => a.mal_id === anime.mal_id);
    const inLibrary = Boolean(entry);

    if (loading) return <p>Chargement…</p>;
    if (error)   return <p className="error">{error}</p>;

    return (
        <div className="anime-detail">
            <header className="anime-header">
                <img src={anime.image_url} alt={anime.title} />
                <div>
                    <h1>{anime.titre}</h1>
                    <p>{anime.synopsis}</p>
                    <div className="progress-container">
                        <div
                            className="progress-bar__fill"
                            style={{
                                width: `${Math.round((progress.seen_episodes / progress.total_episodes) * 100)}%`
                            }}
                        />
                    </div>
                    <p className="progress-label">
                        {progress.seen_episodes} / {progress.total_episodes} épisodes vus
                    </p>
                    <button
                        onClick={() =>
                            inLibrary
                                ? removeAnime(anime.mal_id)    // on passe bien le mal_id pour lookup
                                : addAnime(anime.mal_id)
                        }
                        className="library-button"
                    >
                        {inLibrary ? "Retirer de ma bibliothèque" : "Ajouter à ma bibliothèque"}
                    </button>
                </div>
            </header>

            {
                anime.episodes.length > 0 ? (
                    <section className="episodes-list">
                        {anime.episodes.map(ep => (
                            <div key={ep.id} className="episode">
                                <span>Épisode {ep.numero} — {ep.titre}</span>
                            <button
                                onClick={() => toggleVu(ep.id)}
                                className={ep.pivot?.watched_at ? "seen" : ""}
                            >
                                {ep.pivot?.watched_at ? "Vu ✅" : "Marquer lu"}
                            </button>
                        </div>
                    ))}
                </section>
            ) : (
                <p className="no-episodes">C'est un film, il n'y a pas d'épisodes.</p>
            )}
        </div>
    );
}
