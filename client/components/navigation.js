import {useContext, useState} from "react";
import cn from "classnames";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faBars, faLightbulb, faMoon, faSearch, faTimes} from "@fortawesome/free-solid-svg-icons";
import {ThemeContext} from "../context/themeContext";

export default function Navigation() {
    const [ show, setShow ] = useState(false);
    const { darkTheme, setDarkTheme } = useContext(ThemeContext);

    return (
        <>
            <nav className={cn("navigation", { "--show": show })} onClick={() => setShow(false)}>
                <div className="container navigation__container" onClick={(event) => event.stopPropagation()}>
                    <a className="navigation__logo" href="#">
                        <img className="navigation__logo-image" src="/img/logo.svg" alt="Logo" />
                    </a>
                    <span className="navigation__tab">
                        <div className="navigation__search">
                            <FontAwesomeIcon icon={faSearch} fixedWidth />
                            <input type="text" placeholder="Search" />
                        </div>
                    </span>
                    <span className="navigation__links">
                        <a className="navigation__tab --link" href="#">
                            Topic 1
                        </a>
                        <a className="navigation__tab --link" href="#">
                            Topic 2
                        </a>
                        <a className="navigation__tab --link" href="#">
                            Login
                        </a>
                    </span>
                    <button className="button theme__switch" onClick={() => setDarkTheme(!darkTheme)}>
                        <FontAwesomeIcon icon={darkTheme ? faLightbulb : faMoon} fixedWidth />
                    </button>
                </div>
            </nav>

            <button className={cn("navigation__toggle-mobile", { "--active": show })} onClick={() => setShow(!show)}>
                <FontAwesomeIcon icon={show ? faTimes : faBars} fixedWidth />
            </button>
        </>
    );
}
