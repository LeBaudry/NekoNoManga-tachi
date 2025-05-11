import { useEffect, useState } from "react";
import useManga from "../../../hooks/useManga.jsx";
import "./home.css";

export function Home() {
    const [query, setQuery] = useState("");
    const {
        mangas,
        pagination,
        loading,
        error,
        getMangas,
        searchMangas,
    } = useManga();


    useEffect(() => {
        getMangas();
    }, []);

    const handleSearch = e => {
        e.preventDefault();
        searchMangas(query.trim());
    };

    if (loading) return <p>Chargement…</p>;
    if (error)   return <p className="error">{error}</p>;

    return (
        <>
            <form className="search-form" onSubmit={handleSearch}>
                <input
                    type="text"
                    placeholder="Rechercher un anime…"
                    value={query}
                    onChange={e => setQuery(e.target.value)}
                    className="search-input"
                />
                <button type="submit" className="search-button">Rechercher</button>
            </form>

            <div className="cards">
                {mangas.map(anime => (
                    <div key={anime.mal_id} className="card">
                        <img className="card__img"
                             src={anime.images.jpg.image_url}
                             alt={anime.title} />
                        <div className="card__body">
                            <h3 className="card__title">{anime.title}</h3>
                            <p className="card__synopsis">
                                {anime.synopsis?.slice(0, 100)}…
                            </p>
                        </div>
                    </div>
                ))}
            </div>

            {/* Pagination seulement si on n’est pas en mode recherche */}
            {!query && (
                <div className="pagination">
                    <button
                        onClick={() => getMangas(pagination.page - 1)}
                        disabled={pagination.page <= 1}
                    >
                        Précédent
                    </button>
                    <span>
            Page {pagination.page} / {pagination.last_visible_page}
          </span>
                    <button
                        onClick={() => getMangas(pagination.page + 1)}
                        disabled={!pagination.has_next_page}
                    >
                        Suivant
                    </button>
                </div>
            )}
        </>
    );
}
