import styled from "styled-components";

export const StyledTagList = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;
    flex-wrap: wrap;

    margin: -2.5px ${-0.75 / 2}rem; // Hack to have gutters between items without an outer margin

    & > * {
        margin: 2.5px ${0.75 / 2}rem;
    }
`;
