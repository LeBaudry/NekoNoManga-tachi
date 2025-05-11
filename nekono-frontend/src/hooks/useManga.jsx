import {useState} from "react";
import axios from "axios";

const useManga= () => {

    /* DECLARATION *************************** */
    /* *************************************** */
    const [mangas, setMangas] = useState([])
    const [pagination, setPagination] = useState({
        page: 1,
        per_page: 25,
        total: 0,
        count: 0,
        last_visible_page: 0,
        has_next_page: false,
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState(null);
    const [errors, setErrors] = useState(null);

    /* FONCTIONS ***************************** */
    /* *************************************** */

    const getMangas = async(page = 1) =>{
        setLoading(true);

        try {
            const response = await axios.get("http://localhost:8000/api/jikan/animes",
                {
                    params: {
                        page,
                        limit: pagination.per_page,
                    }
                });
            setMangas(response.data.data);
            setPagination(response.data.pagination);
            setErrors(null);
        }
        catch(err)
        {
            setMangas([])
            setMessage(null);
            setErrors("Erreur : impossible de récupérer les Mangas");
        }
        finally{
            setLoading(false);
        }
    };


    const searchMangas = async(query) =>{
        if (!query) return getMangas();
        setLoading(true);

        try {
            const response = await axios.get("http://localhost:8000/api/jikan/search",
                {
                    params: { q: query, limit: pagination.per_page }
                });
            setMangas(response.data);
            setPagination(p => ({ ...p, page: 1, last_visible_page: 1, has_next_page: false }));
            setErrors(null);
        }
        catch(err)
        {
            setMangas([])
            setMessage(null);
            setErrors("Erreur : Recherche échouée");
        }
        finally{
            setLoading(false);
        }
    };



    /* RENDER ************************* */
    /* ******************************** */

    return {
        mangas, pagination, loading, errors, message,
        getMangas,
        searchMangas,
    }

};


export default useManga;

