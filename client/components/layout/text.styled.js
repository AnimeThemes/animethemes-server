import styled, { css } from "styled-components";

const overwrites = css`
    ${(props) => props.link && css`
        color: ${(props) => props.theme.colors.secondaryTitle};

        &[href]:hover {
            text-decoration: underline;
        }
    `}

    ${(props) => props.block && css`
        display: block;
    `}

    margin: 0;
`;

export const StyledText = styled.span`
    color: ${(props) => props.theme.colors.primaryHighEmphasis};

    ${(props) => props.small && css`
        font-size: 0.8rem;
        font-weight: bold;
    `}

    ${overwrites}
`;

export const StyledTitlePage = styled.h1`
    font-size: 2rem;
    font-weight: bold;
    color: ${(props) => props.theme.colors.primaryTitle};

    ${overwrites}
`;

export const StyledTitleSection = styled.h2`
    font-size: 1rem;
    font-weight: bold;
    color: ${(props) => props.theme.colors.primaryHighEmphasis};
    text-transform: uppercase;

    ${overwrites}
`;

export const StyledTitleCard = styled.span`
    font-weight: bold;
    color: ${(props) => props.theme.colors.primaryTitle};

    ${overwrites}
`;
