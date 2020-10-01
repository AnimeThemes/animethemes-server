import {useEffect, useState} from "react";
import SearchInput from "../../components/searchInput";
import {useDebounce} from "use-debounce";
import styled from "styled-components";
import {gapsColumn} from "../../styles/utils/gaps";
import {useRouter} from "next/router";
import GlobalSearch from "../../components/globalSearch";
import {StyledText, StyledTitlePage} from "../../components/layout/text.styled";
import useSearch from "../../hooks/useSearch";

const StyledSearchPage = styled.div`
    ${gapsColumn("1.5rem")}
`;
const StyledSearchResults = styled.div`
    ${gapsColumn("1rem")}
`;

export default function SearchPage({ entity }) {
    const router = useRouter();

    const [ searchQuery, setSearchQuery ] = useState("");
    const [ debouncedSearchQuery ] = useDebounce(searchQuery, 500);

    const [ results, isSearching ] = useSearch(entity, debouncedSearchQuery);

    // Not to be confused with searchQuery. This is used for the URL only.
    const query = searchQuery ? { q: searchQuery } : {};

    // The order of the effects is important! This effect needs to be above the replacement of the url.
    useEffect(() => {
        // Replace with Next.js API as soon as Router.isReady becomes a thing.
        const urlParams = new URLSearchParams(location.search);
        setSearchQuery(urlParams.get("q") || "");
    }, []);

    useEffect(() => {
        // Update URL to maintain the searchQuery on page navigation.
        router.replace(
            { pathname: "/search/[[...path]]", query },
            { pathname: entity ? `/search/${entity}` : "/search", query }
        );
    }, [ searchQuery ]);

    return (
        <StyledSearchPage>
            <StyledTitlePage>Search {entity || "global"}</StyledTitlePage>
            <SearchInput query={searchQuery} setQuery={setSearchQuery} isSearching={isSearching}/>
            <StyledSearchResults>
                {!debouncedSearchQuery ? (
                    <StyledText block>Type in a query to begin searching.</StyledText>
                ) : !!results && (
                    <GlobalSearch results={results} searchEntity={entity} searchQuery={searchQuery}/>
                )}
            </StyledSearchResults>
        </StyledSearchPage>
    );
}

export async function getStaticPaths() {
    return {
        paths: [
            { params: { path: [] } },
            { params: { path: [ "anime" ] } },
            { params: { path: [ "theme" ] } },
            { params: { path: [ "artist" ] } }
        ],
        fallback: false,
    };
}

export async function getStaticProps({ params }) {
    const { path } = params;

    return {
        props: {
            entity: path && path.length ? path[0] : null
        },
    };
}
