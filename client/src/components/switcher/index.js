import React from "react";
import styled from "styled-components";
import elevatedPrimaryBackground from "styles/helper";
import {gapsRow} from "styles/mixins";

const StyledSwitcher = styled.div`
    display: flex;
    flex-direction: row;

    background-color: ${elevatedPrimaryBackground};

    border-radius: 1rem;

    ${gapsRow("0.5rem")}
`;

export default function Switcher({ children, ...props }) {
    return (
        <StyledSwitcher {...props}>
            {children}
        </StyledSwitcher>
    );
}
