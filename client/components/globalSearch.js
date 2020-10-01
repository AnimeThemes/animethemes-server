import AnimeSearchResultCard from "./animeSearchResultCard";
import ThemeSearchResultCard from "./themeSearchResultCard";
import ArtistSearchResultCard from "./artistSearchResultCard";
import Link from "next/link";
import {StyledButton} from "./layout/button.styled";
import {StyledText, StyledTitleSection} from "./layout/text.styled";

export default function GlobalSearch({ results, searchQuery, searchEntity }) {
    const query = searchQuery ? { q: searchQuery } : {};

    const {animeResults, themeResults, artistResults} = results;
    const totalResults = animeResults.length + themeResults.length + artistResults.length;

    if (!totalResults) {
        return (
            <StyledText block>No results found for query "{searchQuery}". Did you spell it correctly?</StyledText>
        );
    }

    return (
        <>
            <EntitySearch searchEntity={searchEntity} query={query} entity="anime" title="Anime" results={animeResults}/>
            <EntitySearch searchEntity={searchEntity} query={query} entity="theme" title="Themes" results={themeResults}/>
            <EntitySearch searchEntity={searchEntity} query={query} entity="artist" title="Artist" results={artistResults}/>
        </>
    );
}

function EntitySearch({ searchEntity, query, entity, title, results }) {
    if ((searchEntity !== null && searchEntity !== entity) || !results.length) {
        return null;
    }

    let resultCards = results.map((result) => {
        switch (entity) {
            case "anime":
                return <AnimeSearchResultCard key={result.id} anime={result}/>;
            case "theme":
                return <ThemeSearchResultCard key={result.id} theme={result}/>;
            case "artist":
                return <ArtistSearchResultCard key={result.id} artist={result}/>;
        }
    });

    return (
        <>
            {searchEntity === null && (
                <StyledTitleSection>{title}</StyledTitleSection>
            )}
            {resultCards}
            {searchEntity === null && (
                <Link href={{ pathname: "/search/[[...path]]", query }} as={{ pathname: `/search/${entity}`, query }} passHref>
                    <StyledButton silent>Show more</StyledButton>
                </Link>
            )}
        </>
    );
}
