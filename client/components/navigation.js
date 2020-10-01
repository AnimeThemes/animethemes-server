import {useContext, useState} from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faBars, faLightbulb, faMoon, faTimes} from "@fortawesome/free-solid-svg-icons";
import {ThemeContext} from "styled-components";
import SearchInput from "./searchInput";
import {
    StyledLinks,
    StyledLogo,
    StyledLogoContainer,
    StyledMobileToggle,
    StyledNavigation,
    StyledNavigationContainer
} from "./navigation.styled";
import Elevator from "./elevator";
import asset from "../utils/asset";
import {StyledButton} from "./layout/button.styled";

export default function Navigation({ toggleTheme }) {
    const [ show, setShow ] = useState(false);
    const theme = useContext(ThemeContext);

    return (
        <>
            <StyledNavigation show={show} onClick={() => setShow(false)}>
                <StyledNavigationContainer onClick={(event) => event.stopPropagation()}>
                    <Elevator>
                        <StyledLogoContainer as="a" link href="#">
                            <StyledLogo className="navigation__logo-image" src={asset("/img/logo.svg")} alt="Logo" />
                        </StyledLogoContainer>
                        <SearchInput/>
                        <StyledLinks>
                            <StyledButton silent>Topic 1</StyledButton>
                            <StyledButton silent>Topic 2</StyledButton>
                            <StyledButton silent>Login</StyledButton>
                        </StyledLinks>
                        <StyledButton silent icon onClick={toggleTheme}>
                            <FontAwesomeIcon icon={theme.isDark ? faLightbulb : faMoon} fixedWidth />
                        </StyledButton>
                    </Elevator>
                </StyledNavigationContainer>
            </StyledNavigation>

            <StyledMobileToggle active={show} onClick={() => setShow(!show)}>
                <FontAwesomeIcon icon={show ? faTimes : faBars} fixedWidth />
            </StyledMobileToggle>
        </>
    );
}
