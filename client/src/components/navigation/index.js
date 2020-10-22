import React, {useEffect, useState} from "react";
import {withPrefix} from "gatsby";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faBars, faLightbulb, faMoon, faSpinner, faTimes} from "@fortawesome/free-solid-svg-icons";
import {
    StyledLinks,
    StyledLogo,
    StyledLogoContainer,
    StyledMobileToggle,
    StyledNavigation,
    StyledNavigationContainer, StyledQuickSearch
} from "./style";
import Elevator from "components/elevator";
import Button from "components/button";
import {navigate} from "gatsby";

export default function Navigation() {
    const [ show, setShow ] = useState(false);
    const [ theme, setTheme ] = useState(null);

    useEffect(() => {
        const body = document.body;

        setTheme(body.getAttribute("theme"));
    }, []);

    function toggleTheme() {
        const newTheme = theme === "dark" ? "light" : "dark";

        setTheme(newTheme);

        const body = document.body;
        body.setAttribute("theme", newTheme);

        window.localStorage.setItem("theme", newTheme);
    }

    return (
        <>
            <StyledNavigation show={show} onClick={() => setShow(false)}>
                <StyledNavigationContainer onClick={(event) => event.stopPropagation()}>
                    <Elevator>
                        <StyledLogoContainer as="a" link href="#">
                            <StyledLogo className="navigation__logo-image" src={withPrefix("/img/logo.svg")} alt="Logo" />
                        </StyledLogoContainer>
                        {/* This will later be replaced with an actual quick search */}
                        <StyledQuickSearch setQuery={(query) => navigate(`/search?q=${query}`, { replace: true })}/>
                        <StyledLinks>
                            {/* Other links */}
                            <Button silent icon onClick={toggleTheme}>
                                <FontAwesomeIcon icon={theme === null ? faSpinner : theme === "dark" ? faLightbulb : faMoon} spin={theme === null} fixedWidth />
                            </Button>
                        </StyledLinks>
                    </Elevator>
                </StyledNavigationContainer>
            </StyledNavigation>

            <StyledMobileToggle active={show} onClick={() => setShow(!show)}>
                <FontAwesomeIcon icon={show ? faTimes : faBars} fixedWidth />
            </StyledMobileToggle>
        </>
    );
}
