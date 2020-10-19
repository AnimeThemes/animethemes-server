import {useEffect, useMemo, useState} from "react";
import styled from "styled-components";
import {navigate} from "gatsby";
import {useDebounce} from "use-debounce";
import SearchInput from "components/input/search";
import GlobalSearch from "components/search/global";
import {gapsColumn} from "styles/mixins";
import Title from "components/text/title";

const StyledSearchPage = styled.div`
    ${gapsColumn("1.5rem")}
`;

export default function SearchPage({ location: { pathname, search } }) {
    const entity = useMemo(() => pathname.match(/\/search(?:\/(.+))?/)[1], [ pathname ]);
    const urlParams = useMemo(() => new URLSearchParams(search), [ search ]);

    const [ searchQuery, setSearchQuery ] = useState(urlParams.get("q") || "");
    const [ debouncedSearchQuery ] = useDebounce(searchQuery, 500);

    // Temporary effect to listen for changes to the search query that may be made by the quick search (WIP)
    useEffect(() => { setSearchQuery(urlParams.get("q")) }, [ urlParams ]);

    useEffect(() => {
        // Update URL to maintain the searchQuery on page navigation.
        const newUrlParams = new URLSearchParams();
        if (searchQuery) {
            newUrlParams.set("q", searchQuery);
        }
        const params = newUrlParams.toString();

        let url = "/search";
        if (entity) {
            url += `/${entity}`;
        }
        if (params) {
            url += `?${params}`;
        }

        navigate(url, { replace: true });
    }, [ entity, searchQuery ]);

    return (
        <StyledSearchPage>
            <Title>Search</Title>
            <SearchInput query={searchQuery} setQuery={setSearchQuery} isSearching={false}/>
            <GlobalSearch searchEntity={entity} searchQuery={debouncedSearchQuery}/>
        </StyledSearchPage>
    );
}
