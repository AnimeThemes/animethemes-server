import styled from "styled-components";
import {gapsRow} from "../styles/utils/gaps";

export const StyledSearchInput = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;

    ${gapsRow("0.5rem")}

    padding: 0.5rem 1rem;
    border-radius: 2rem;

    background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};
    color: ${(props) => props.theme.colors.primaryMediumEmphasis};
`;
