import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import {RouterProvider} from "react-router-dom";
import {router} from "./services/Router.jsx";


createRoot(document.getElementById('layout')).render(
    <StrictMode>
        <RouterProvider router={router} />
    </StrictMode>,
)
