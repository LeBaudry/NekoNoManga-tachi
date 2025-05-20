import useAuth from "../../../hooks/useAuth.jsx";
import {NavLink, useNavigate} from "react-router-dom";


export function Header() {
    const { getLoggedOut } = useAuth();


    const handleLogout = () => {
        getLoggedOut();
        navigate('/login', { replace: true });
    };


    return (
        <header className="header">
            <div className="header__center">
                <div className="header_logo">NekoNoMangaTachi</div>
                <nav className="header_nav">
                    <NavLink
                        to="/"
                        className={({isActive}) =>
                            `header_nav__link${isActive ? ' header_nav__link--active' : ''}`
                        }
                    >
                        Home
                    </NavLink>
                    <NavLink
                        to="/library"
                        className={({isActive}) =>
                            `header_nav__link${isActive ? ' header_nav__link--active' : ''}`
                        }
                    >
                        Ma bibliothèque
                    </NavLink>
                </nav>
            </div>

            <div className="header_actions">
                <button onClick={handleLogout}>Déconnexion</button>
            </div>
        </header>

    )

}