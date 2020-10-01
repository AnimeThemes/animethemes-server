import styled, {css} from "styled-components";
import {StyledText} from "./layout/text.styled";

export const StyledAnimeSynopsis = styled.div`
    overflow: hidden;
    transition: height 250ms;
`

export const StyledCollapsableText = styled(StyledText)`
    ${(props) => props.collapsed && css`
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    `}
`
