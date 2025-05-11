
import "./layout.css";
import { Outlet} from "react-router-dom";
import {Header} from "./Header.jsx";


export function Layout(){



    return (
        <div>
            <Header />
            <div className="outlet_container">
                <Outlet/>
            </div>
        </div>
    )
}