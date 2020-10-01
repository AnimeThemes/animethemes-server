import styled from "styled-components";
import {StyledCard} from "./layout/card.styled";

export const StyledSearchResultCard = styled(StyledCard)`
    display: flex;
    flex-direction: row;
    align-items: center;

    margin-top: 1rem;
    padding: 0 1rem 0 0;
`;

export const StyledCover = styled.img`
    width: 48px;
    height: 64px;
    object-fit: cover;
`;

export const StyledBody = styled.div`
    flex: 1;

    display: flex;
    flex-direction: column;
    justify-content: center;

    //@include gaps(0.25rem, v);

    padding: 0 1rem;
`;
