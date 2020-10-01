import styled from "styled-components";
import {StyledButton} from "./layout/button.styled";

export const StyledButtonPlay = styled(StyledButton)`
    display: flex;
    flex-direction: row;
    align-items: stretch;

    padding: 0;
`;

export const StyledPrefix = styled.div`
    display: inline-flex;
    justify-content: center;
    align-items: center;

    width: 2rem;
    height: 2rem;
    border-radius: 1rem;

    background-color: ${(props) => props.theme.colors.secondaryBackground};
    color: ${(props) => props.theme.colors.primaryBackground[0]};
`;

export const StyledBody = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;

    padding: 0 0.75rem 0 0.5rem;
`;
