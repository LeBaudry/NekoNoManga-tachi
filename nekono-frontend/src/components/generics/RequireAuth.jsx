import { Navigate, useLocation } from 'react-router-dom';

export function RequireAuth({ children }) {

    const isLoggedIn = Boolean(localStorage.getItem('token'));
    const location = useLocation();

    if (!isLoggedIn) {

        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    return children;
}