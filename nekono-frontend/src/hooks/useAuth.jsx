import {useEffect, useState} from "react";
import axios from "axios";
import {Navigate} from "react-router-dom";



const checkUserLoggedIn = () => {
    return localStorage.getItem('token') !== null;
};



const useAuth = () => {
    /* DECLARATION *************************** */
    /* *************************************** */

    const [loginMessage, setLoginMessage] = useState(null);
    const [loginErrors, setLoginErrors] = useState(null);
    const [loading, setLoading] = useState(false);

    const [user, setUser] = useState(() => {
        const raw = localStorage.getItem("user");
        if (typeof raw === "string") {
            try {
                return JSON.parse(raw);
            } catch {
                console.warn("useAuth: impossible de parser 'user' en localStorage:", raw);
                return null;
            }
        }
        return null;
    });
    const isAuthenticated = Boolean(user);

    /* CYCLE DE VIE ************************** */
    /* *************************************** */


    /* FONCTIONS ***************************** */
    /* *************************************** */



    const register = async (formData) => {
        setLoading(true);
        try {
            await axios.post("http://localhost:8000/api/register", formData);

            setLoginMessage("Inscription réussie");
            setLoginErrors(null);
            return true;
        } catch (error) {
            console.error(error);
            setLoginMessage(null);
            setLoginErrors("Erreur : échec lors de la connexion");
            return false;
        } finally {
            setLoading(false);
        }
    };


    const getLoggedIn = async (formData) => {
        setLoading(true);
        try {
            // on récupère la réponse complète
            const response = await axios.post("http://localhost:8000/api/login", formData);

            // on extrait user et token depuis response.data
            const { user: me, token } = response.data;

            setLoginMessage("Connexion réussie");
            setLoginErrors(null);

            // on stocke en localStorage
            localStorage.setItem("token", token);
            localStorage.setItem("user", JSON.stringify(me));
            setUser(me);

            // et on peut rediriger
            navigate("/", { replace: true });
        } catch (error) {
            console.error(error);
            setLoginMessage(null);
            setLoginErrors("Erreur : échec lors de la connexion");
        } finally {
            setLoading(false);
        }
    };

    const getLoggedOut = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        //  cookies.removeItem('permissions');
    }


    /* RENDER ************************* */
    /* ******************************** */
    return {
        user, isAuthenticated, loading, loginErrors, loginMessage,
        getLoggedIn,
        getLoggedOut,
        register,
    };
};

export default useAuth;
