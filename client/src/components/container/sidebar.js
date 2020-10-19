import React from "react";
import styled from "styled-components";

const StyledContainerSidebar = styled.div`
    display: flex;
    flex-direction: row;

    @media (max-width: 720px) {
        flex-direction: column;
    }
`;
const StyledSidebar = styled.div`
    flex: 1;

    @media (min-width: 721px) {
        margin-right: 2rem;
    }
`;
const StyledBody = styled.div`
    flex: 3;
`;

export default function ContainerSidebar({ sidebar, children, ...props }) {
    return (
        <StyledContainerSidebar {...props}>
           <StyledSidebar>
               {sidebar}
           </StyledSidebar>
           <StyledBody>
               {children}
           </StyledBody>
       </StyledContainerSidebar>
    );
}
