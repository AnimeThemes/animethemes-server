import SearchResultCard from "./searchResultCard";
import Elevator from "./elevator";
import ButtonPlay from "./buttonPlay";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faEllipsisH} from "@fortawesome/free-solid-svg-icons";
import useAniList from "../hooks/useAniList";
import {StyledButton} from "./layout/button.styled";
import {StyledTagList} from "./layout/tagList.styled";
import {StyledText} from "./layout/text.styled";

export default function AnimeSearchResultCard({ anime }) {
    const { image } = useAniList(anime);

    let premiere = anime.year;
    if (anime.season) {
        premiere = anime.season + " " + premiere;
    }

    return (
        <SearchResultCard
            title={anime.name}
            description={`Anime • ${premiere} • ${anime.themes.length} themes`}
            image={image}
            href="/anime/[slug]"
            as={`/anime/${anime.alias}`}
        >
            <Elevator>
                <StyledTagList>
                    {anime.themes.slice(0, 4).map((theme) => (
                        <ButtonPlay key={theme.id} href="/theme/[id]" as={`/theme/${theme.id}`}>
                            <StyledText small>{theme.slug}</StyledText>
                        </ButtonPlay>
                    ))}
                    {anime.themes.length > 4 && (
                        <StyledButton icon title="Show all themes">
                            <FontAwesomeIcon icon={faEllipsisH} fixedWidth/>
                        </StyledButton>
                    )}
                </StyledTagList>
            </Elevator>
        </SearchResultCard>
    );
}
