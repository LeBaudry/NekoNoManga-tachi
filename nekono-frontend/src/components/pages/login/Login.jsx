import useAuth from "../../../hooks/useAuth.jsx";
import {useEffect, useState} from "react";
import {Link, Navigate, useNavigate} from "react-router-dom";
import './login.css'

export function Login() {

    const navigate = useNavigate();
    const { getLoggedIn, isAuthenticated, loading, loginErrors   } = useAuth()
    const [formData, setFormData] = useState({
        login: "",
        password: ""
    });


    useEffect(() => {
        if (isAuthenticated) {
            navigate("/", { replace: true });
        }
    }, [isAuthenticated, navigate]);

    const handleChange = (e) => {
        setFormData((f) => ({
            ...f,
            [e.target.name]: e.target.value
        }));
    };


    const handleSubmit = (e) => {
        e.preventDefault();
        getLoggedIn({
            email: formData.login,
            password: formData.password
        });
    };


    return (
        <div className="login_page">

            <div className="login_container">
                <div className="login_logo">
                    <div className="login_logo_text">NekoNoMangaTachi</div>
                </div>
                {/* Formulaire */}
                <form className="login_form" onSubmit={handleSubmit}>

                    <input
                        name="login"
                        value={formData.login}
                        onChange={handleChange}
                        className="login_form_input"
                        type="text"
                        placeholder="Email"
                        required
                    />
                    <input
                        name="password"
                        value={formData.password}
                        onChange={handleChange}
                        className="login_form_input"
                        type="password"
                        placeholder="Mot de passe"
                        required
                    />
                    <input
                        type="submit"
                        className="login_form_button"
                        value="connexion"
                    />
                </form>
                <div className="login_register">
                    Pas encore inscrit ?{" "}
                     <Link to="/register" className="login_register_link">
                     Inscrivez-vous
                     </Link>
                </div>
            </div>
        </div>
    )
}