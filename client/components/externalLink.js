import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronCircleRight} from "@fortawesome/free-solid-svg-icons";

function ExternalLink({ children, href }) {
    return (
        <a href={href} target="_blank" className="external-link">
            <span>{children}</span>
            <FontAwesomeIcon icon={faChevronCircleRight} className="external-link__icon"/>
        </a>
    );
}

export default ExternalLink;
