import React from "react";
import styled, {css} from "styled-components";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {gapsRow} from "styles/mixins";
import Text from "components/text";

const StyledTag = styled.span`
    display: inline-flex;
    flex-direction: row;
    align-items: center;

    ${gapsRow("0.25rem")}
`;
const StyledTagIcon = styled(FontAwesomeIcon)`
    color: ${(props) => props.theme.colors.primaryLowEmphasis};

    ${(props) => props.warning && css`
        color: ${(props) => props.theme.colors.warningTitle};
    `}
`;

export default function Tag({ icon, iconProps, warning, children, ...props }) {
    return (
        <StyledTag {...props}>
            {!!icon && (
                <StyledTagIcon icon={icon} fixedWidth warning={warning} {...iconProps}/>
            )}
            {!!children && (
                <Text small>
                    {children}
                </Text>
            )}
        </StyledTag>
    );
}
