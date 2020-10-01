import styled from "styled-components";

export const StyledDescriptionList = styled.dl`
    display: flex;
    flex-direction: column;
    align-items: center;

    margin: 0;
`;

export const StyledKey = styled.dt`
    margin: 0 0 0.25rem 0;
`;

export const StyledValue = styled.dd`
    margin: 0;

    &:not(:last-child) {
        margin-bottom: 1.5rem;
    }
`;
