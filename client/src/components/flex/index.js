import React from "react";
import styled, {css} from "styled-components";
import {gapsColumn, gapsRow} from "styles/mixins";

const StyledFlex = styled.div`
    display: flex;
    flex-direction: ${(props) => props.row ? "row" : "column"};
    flex-wrap: ${(props) => props.wrap ? "wrap" : "nowrap"};
    justify-content: ${(props) => props.justifyContent || "flex-start"};
    align-items: ${(props) => props.alignItems || "stretch"};

    ${(props) => props.gapsRow && gapsRow(props.gapsRow)}
    ${(props) => props.gapsColumn && gapsColumn(props.gapsColumn)}
    ${(props) => props.gapsBoth && css`
        // Hack to have gutters between items without an outer margin
        margin: calc(${props.gapsBoth} / -2);

        & > * {
            margin: calc(${props.gapsBoth} / 2);
        }
    `}
`;
const StyledFlexItem = styled.div`
    flex: ${(props) => props.flex || "0 0 auto"};
    align-self: ${(props) => props.alignSelf || "auto"};
    justify-self: ${(props) => props.justifySelf || "auto"};
`;

export default function Flex({ children, ...props }) {
    return (
        <StyledFlex {...props}>
            {children}
        </StyledFlex>
    );
}

export function FlexItem({ children, ...props }) {
    return (
        <StyledFlexItem {...props}>
            {children}
        </StyledFlexItem>
    );
}
