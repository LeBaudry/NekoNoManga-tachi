import {createBrowserRouter} from "react-router-dom";
import {Layout} from "../components/pages/layout/Layout.jsx";
import {Home} from "../components/pages/home/Home.jsx";

import {Login} from "../components/pages/login/Login.jsx";
import {Library} from "../components/pages/library/Library.jsx";
import {RequireAuth} from "../components/generics/RequireAuth.jsx";
import {Register} from "../components/pages/register/Register.jsx";
import {AnimeDetail} from "../components/pages/animeDetail/AnimeDetail.jsx";




export const router = createBrowserRouter([
    {
        path: "/",
        element: <RequireAuth><Layout/></RequireAuth> ,
        children:[
            {
                index: true,
                element: <Home />,
            },
            {
                path: "library",
                element: <Library />,
            }
            ,
            {
                path: "anime/:id",
                element: <AnimeDetail />,
            }
        ]
    },
    {
        path: "/login",
        element: <Login/>,
    },
    {
        path: "/register",
        element: <Register/>,
    },

]);