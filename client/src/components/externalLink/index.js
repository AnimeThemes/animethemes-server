import React from "react";
import styled from "styled-components";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronCircleRight} from "@fortawesome/free-solid-svg-icons";
import Text from "components/text";
import {gapsRow} from "styles/mixins";

const StyledExternalLink = styled(Text).attrs({
    link: true
})`
    ${gapsRow("0.25rem")}
`;

export default function ExternalLink({ href, children, ...props }) {
    return (
        <StyledExternalLink href={href} target="_blank" {...props}>
            <span>{children}</span>
            <FontAwesomeIcon icon={faChevronCircleRight}/>
        </StyledExternalLink>
    );
}
