import styled, {css} from "styled-components";

export const StyledButton = styled.button`
    cursor: pointer;

    background-color: ${(props) => props.active
        ? props.theme.colors.secondaryBackground
        : props.theme.colors.primaryBackground[props.theme.elevation - (props.silent ? 1 : 0)]
    };
    color: ${(props) => props.active ? props.theme.colors.primaryBackground[0] : props.theme.colors.primaryMediumEmphasis};

    border-radius: 1rem;
    padding: ${(props) => props.icon ? "0.5rem" : "0.5rem 1rem"};

    ${(props) => !props.active && css`
        &:hover {
            background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation + 1 - (props.silent ? 1 : 0)]};
            color: ${(props) => props.theme.colors.primaryHighEmphasis};
        }
    `}
`;
