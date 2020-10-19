import React from "react";
import {Link} from "gatsby";
import Text from "components/text";
import Title from "components/text/title";
import styled from "styled-components";
import Card from "components/card/index";

const StyledSearchResultCard = styled(Card)`
    display: flex;
    flex-direction: row;
    align-items: center;

    padding: 0 1rem 0 0;
`;
const StyledCover = styled.img`
    width: 48px;
    height: 64px;
    object-fit: cover;
`;
const StyledBody = styled.div`
    flex: 1;

    display: flex;
    flex-direction: column;
    justify-content: center;

    padding: 0 1rem;
`;
const StyledChildren = styled.div`
    @media (max-width: 720px) {
        display: none;
    }
`;

export default function SearchResultCard({ title, description, image, to, children, ...props }) {
    const card = (
        <StyledSearchResultCard {...props}>
            <StyledCover alt="Cover" src={image}/>
            <StyledBody>
                <Title variant="card" link maxLines={2}>{title}</Title>
                <Text small maxLines={1}>{description}</Text>
            </StyledBody>
            <StyledChildren>
                {children}
            </StyledChildren>
        </StyledSearchResultCard>
    );

    if (to) {
        return (
            <Link to={to}>
                {card}
            </Link>
        );
    }

    return card;
}
