import styled from "styled-components";
import {gapsColumn} from "../styles/utils/gaps";

export const StyledSidebarView = styled.div`
    display: flex;
    flex-direction: row;

    @media (max-width: 720px) {
        flex-direction: column;
    }
`;

export const StyledSidebar = styled.div`
    flex: 1;

    display: flex;
    flex-direction: column;

    ${gapsColumn("1.5rem")}

    @media (min-width: 721px) {
        margin-right: 2rem;
    }
`;

export const StyledBody = styled.div`
    flex: 3;

    ${gapsColumn("1rem")}
`;
