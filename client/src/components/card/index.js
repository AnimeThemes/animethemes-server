import React from "react";
import styled, {css} from "styled-components";

const StyledCard = styled.div`
    padding: 1rem 1.5rem;
    border-left: 4px solid ${(props) => props.theme.colors.secondaryTitle};
    border-radius: 0 0.5rem 0.5rem 0;

    background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};

    ${(props) => props.hoverable && css`
        cursor: pointer;

        &:hover {
            background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation + 1]};
        }
    `}
`;

export default function Card({ children, ...props }) {
    return (
        <StyledCard {...props}>
            {children}
        </StyledCard>
    )
}
