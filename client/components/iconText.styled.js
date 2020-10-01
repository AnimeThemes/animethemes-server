import styled, {css} from "styled-components";
import {gapsRow} from "../styles/utils/gaps";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export const StyledIconText = styled.span`
    display: inline-flex;
    flex-direction: row;
    align-items: center;

    ${gapsRow("0.25rem")}
`;

export const StyledIcon = styled(FontAwesomeIcon)`
    color: ${(props) => props.theme.colors.primaryLowEmphasis};

    ${(props) => props.warning && css`
        color: ${(props) => props.theme.colors.warningTitle};
    `}
`;
