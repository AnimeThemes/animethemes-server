import React from "react";
import styled from "styled-components";
import {StyledTextBase} from "components/text/style";

const StyledTitlePage = styled(StyledTextBase).attrs((props) => ({
    as: "h1",
    color: props.theme.colors.primaryTitle
}))`
    font-size: 2rem;
    font-weight: bold;
`;
const StyledTitleSection = styled(StyledTextBase).attrs((props) => ({
    as: "h2",
    color: props.theme.colors.primaryHighEmphasis
}))`
    font-size: 1rem;
    font-weight: bold;
    text-transform: uppercase;
`;
const StyledTitleCard = styled(StyledTextBase).attrs((props) => ({
    color: props.theme.colors.primaryTitle
}))`
    font-weight: bold;
`;

export default function Title({ variant = "page", children, ...props }) {
    switch (variant) {
        case "section":
            return (
                <StyledTitleSection {...props}>
                    {children}
                </StyledTitleSection>
            );
        case "card":
            return (
                <StyledTitleCard {...props}>
                    {children}
                </StyledTitleCard>
            );
        case "page":
        default:
            return (
                <StyledTitlePage {...props}>
                    {children}
                </StyledTitlePage>
            );
    }
}
