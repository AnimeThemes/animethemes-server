import styled, { css } from "styled-components";

export const StyledTextBase = styled.span.attrs((props) => ({
    as: props.as || props.link ? "a" : "span"
}))`
    color: ${(props) => props.color || "inherit"};

    ${(props) => props.link && css`
        color: ${(props) => props.theme.colors.secondaryTitle};
    `}

    ${(props) => props.block && css`
        display: block;
    `}

    ${(props) => props.maxLines && css`
        display: -webkit-box;
        -webkit-line-clamp: ${props.maxLines};
        -webkit-box-orient: vertical;
        overflow: hidden;
    `}

    margin: 0;
`;
