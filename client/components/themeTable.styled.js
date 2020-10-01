import styled, {css} from "styled-components";
import {gapsColumn, gapsRow} from "../styles/utils/gaps";

export const StyledThemeTable = styled.div`
    ${gapsColumn()}
`;

export const StyledGroupTabs = styled.div`
    display: flex;
    flex-direction: row;

    ${(props) => props.center && css`
        justify-content: center;
    `}

    ${gapsRow()}
`;
