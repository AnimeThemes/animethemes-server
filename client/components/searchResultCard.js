import {StyledBody, StyledCover, StyledSearchResultCard} from "./searchResultCard.styled";
import Link from "next/link";
import {StyledText, StyledTitleCard} from "./layout/text.styled";

export default function SearchResultCard({ title, description, image, children, href, as, ...props }) {
    return (
        <StyledSearchResultCard {...props}>
            <StyledCover alt="Cover" src={image}/>
            <StyledBody>
                <Link href={href} as={as} passHref>
                    <StyledTitleCard as="a" link>{title}</StyledTitleCard>
                </Link>
                <StyledText small>{description}</StyledText>
            </StyledBody>
            {children}
        </StyledSearchResultCard>
    );
}
