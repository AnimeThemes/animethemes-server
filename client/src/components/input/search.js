import React from "react";
import styled from "styled-components";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faSearch, faSpinner} from "@fortawesome/free-solid-svg-icons";
import {gapsRow} from "styles/mixins";

const StyledSearchInput = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;

    ${gapsRow("0.5rem")}

    padding: 0.5rem 1rem;
    border-radius: 2rem;

    background-color: ${(props) => props.theme.colors.primaryBackground[props.theme.elevation]};
    color: ${(props) => props.theme.colors.primaryMediumEmphasis};
`;


export default function SearchInput({ query, setQuery, isSearching, ...props }) {
    return (
        <StyledSearchInput {...props}>
            <FontAwesomeIcon icon={faSearch} fixedWidth />
            <input type="text" placeholder="Search" value={query} onChange={(e) => setQuery(e.target.value)} />
            {isSearching && (
                <FontAwesomeIcon icon={faSpinner} fixedWidth spin />
            )}
        </StyledSearchInput>
    );
}
