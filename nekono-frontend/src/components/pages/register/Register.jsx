import { useState, useEffect } from "react";
import { useNavigate, Link } from "react-router-dom";
import useAuth from "../../../hooks/useAuth.jsx";
import "./register.css";

export function Register() {
    const navigate = useNavigate();
    const { register, loading, loginMessage: message, loginErrors: error, isAuthenticated } = useAuth();

    const [formData, setFormData] = useState({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        if (isAuthenticated) {
            navigate("/", { replace: true });
        }
    }, [isAuthenticated, navigate]);

    const handleChange = (e) => {
        setFormData((f) => ({ ...f, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const success = await register(formData);
        if (success) {
            navigate("/login", { replace: true });
        }
    };

    return (
        <div className="auth_page">
            <div className="auth_container">
                <div className="auth_logo">NekoNoMangaTachi</div>

                {message && <p className="auth_success">{message}</p>}
                {error && <p className="auth_error">{error}</p>}

                <form className="auth_form" onSubmit={handleSubmit}>
                    <input
                        name="name"
                        type="text"
                        placeholder="Nom"
                        value={formData.name}
                        onChange={handleChange}
                        required
                    />
                    <input
                        name="email"
                        type="email"
                        placeholder="Email"
                        value={formData.email}
                        onChange={handleChange}
                        required
                    />
                    <input
                        name="password"
                        type="password"
                        placeholder="Mot de passe"
                        value={formData.password}
                        onChange={handleChange}
                        required
                    />
                    <input
                        name="password_confirmation"
                        type="password"
                        placeholder="Confirmez le mot de passe"
                        value={formData.password_confirmation}
                        onChange={handleChange}
                        required
                    />
                    <button type="submit" disabled={loading}>
                        {loading ? "..." : "S'inscrire"}
                    </button>
                </form>

                <p className="auth_footer">
                    Déjà inscrit ? <Link to="/login">Connectez-vous</Link>
                </p>
            </div>
        </div>
    );
}