import React from "react";
import styled from "styled-components";

const StyledContainer = styled.div`
    margin: 0 auto;
    padding: 1.5rem 1rem;
    max-width: 1100px;
`;

export default function Container({ children, ...props }) {
    return (
        <StyledContainer {...props}>
            {children}
        </StyledContainer>
    );
}
