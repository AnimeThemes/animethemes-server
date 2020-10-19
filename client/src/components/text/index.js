import React from "react";
import styled, { css } from "styled-components";
import {StyledTextBase} from "components/text/style";

const StyledText = styled(StyledTextBase).attrs((props) => ({
    color: props.theme.colors.primaryHighEmphasis
}))`
    ${(props) => props.small && css`
        font-size: 0.8rem;
        font-weight: bold;
    `}
`;

export default function Text({ children, ...props }) {
    return (
        <StyledText {...props}>
            {children}
        </StyledText>
    );
}
