import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faPlay} from "@fortawesome/free-solid-svg-icons";
import {StyledBody, StyledButtonPlay, StyledPrefix} from "./buttonPlay.styled";
import Link from "next/link";

export default function ButtonPlay({ href, as, children }) {
    return (
        <Link href={href} as={as} passHref>
            <StyledButtonPlay>
                <StyledPrefix>
                    <FontAwesomeIcon icon={faPlay} fixedWidth />
                </StyledPrefix>
                <StyledBody>
                    {children}
                </StyledBody>
            </StyledButtonPlay>
        </Link>
    );
}
