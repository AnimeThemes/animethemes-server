import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faSearch, faSpinner} from "@fortawesome/free-solid-svg-icons";
import {StyledSearchInput} from "./searchInput.styled";

export default function SearchInput({ query, setQuery, isSearching }) {
    return (
        <StyledSearchInput>
            <FontAwesomeIcon icon={faSearch} fixedWidth />
            <input type="text" placeholder="Search" value={query} onChange={(e) => setQuery(e.target.value)} />
            {isSearching && (
                <FontAwesomeIcon icon={faSpinner} fixedWidth spin />
            )}
        </StyledSearchInput>
    );
}
