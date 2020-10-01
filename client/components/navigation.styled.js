import styled, {css} from "styled-components";
import {StyledContainer} from "./layout/container.styled";
import {gapsRow, gapsColumn} from "../styles/utils/gaps";

export const StyledNavigation = styled.nav`
    background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};

    @media (max-width: 720px) {
        display: flex;
        align-items: center;

        opacity: 0;
        pointer-events: none;

        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        background-color: rgba(0, 0, 0, 0.5);

        transition: opacity 250ms;

        ${(props) => props.show && css`
            opacity: 1;
            pointer-events: initial;
        `}
    }
`;

export const StyledNavigationContainer = styled(StyledContainer)`
    display: flex;
    flex-direction: row;
    align-items: stretch;

    ${gapsRow("1rem")}

    padding: 0.5rem 1rem;

    @media (max-width: 720px) {
        flex-direction: column;
        align-items: center;

        ${gapsColumn("1rem")}

        padding: 1rem;
        border-radius: 1rem;

        background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};

        ${(props) => props.theme.isDark && css`
            border: 2px solid ${(props) => props.theme.colors.primaryTitle};
        `}
    }
`;

export const StyledLinks = styled.span`
    display: flex;
    flex-direction: row;
`;

export const StyledLogoContainer = styled.div`
    display: flex;
    align-items: center;

    margin-right: auto;

    @media (max-width: 720px) {
        margin-right: 0;
    }
`;

export const StyledLogo = styled.img`
    height: 2rem;

    ${(props) => props.theme.isDark && css`
        filter: invert();
    `}
`;

export const StyledMobileToggle = styled.button`
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    padding: 1rem;
    border-radius: 2rem;

    background-color: ${(props) => props.theme.colors.secondaryBackground};
    color: ${(props) => props.theme.colors.primaryBackground[0]};
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);

    @media (min-width: 721px) {
        display: none;
    }

    ${(props) => props.active && css`
        background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};
        color: ${(props) => props.theme.colors.primaryTitle};

        ${(props) => props.theme.isDark && css`
            margin: -2px;
            border: 2px solid ${(props) => props.theme.colors.primaryTitle};;
        `}
    `}
`;
