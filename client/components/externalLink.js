import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronCircleRight} from "@fortawesome/free-solid-svg-icons";
import {StyledExternalLink} from "./externalLink.styled";

function ExternalLink({ children, href }) {
    return (
        <StyledExternalLink as="a" link href={href} target="_blank">
            <span>{children}</span>
            <FontAwesomeIcon icon={faChevronCircleRight}/>
        </StyledExternalLink>
    );
}

export default ExternalLink;
