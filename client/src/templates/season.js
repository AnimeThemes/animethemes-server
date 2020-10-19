import React from "react";
import {Link} from "gatsby";
import styled from "styled-components";
import AnimeSearchResultCard from "components/card/searchResult/anime";
import Title from "components/text/title";
import {gapsColumn} from "styles/mixins";
import Button from "components/button";
import Flex from "components/flex";
import Switcher from "components/switcher";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faChevronDown} from "@fortawesome/free-solid-svg-icons";

const StyledPage = styled.div`
    ${gapsColumn()}
`;
const StyledYearContainer = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;
`;
const StyledYear = styled.div`
    flex: 1;

    display: flex;

    margin: 0 1rem;
`;
const StyledYearPrevious = styled(StyledYear)`
    justify-content: flex-end;
`;
const StyledYearNext = styled(StyledYear)`
    justify-content: flex-start;
`;

const seasonTitles = {
    winter: "Winter",
    spring: "Spring",
    summer: "Summer",
    fall: "Fall"
};

export default function SeasonIndexPage({ pageContext: { animeList, year, season, yearList, seasonList } }) {
    const previousYear = yearList.indexOf(year) > 0 ? yearList[yearList.indexOf(year) - 1] : null;
    const nextYear = yearList.indexOf(year) < yearList.length - 1 ? yearList[yearList.indexOf(year) + 1] : null;

    return (
        <StyledPage>
            <StyledYearContainer>
                <StyledYearPrevious>
                    {previousYear && (
                        <Link to={`/year/${previousYear}`}>
                            <Button silent>{previousYear}</Button>
                        </Link>
                    )}
                </StyledYearPrevious>
                <Button to="/year" silent>
                    <Title>{year}</Title>
                </Button>
                <StyledYearNext>
                    {nextYear && (
                        <Link to={`/year/${nextYear}`}>
                            <Button silent>{nextYear}</Button>
                        </Link>
                    )}
                </StyledYearNext>
            </StyledYearContainer>
            <Flex row justifyContent="center">
                <Switcher>
                    {seasonList.map((availableSeason) => (
                        <Button to={`/year/${year}/${availableSeason}`} active={availableSeason === season}>{seasonTitles[availableSeason]}</Button>
                    ))}
                </Switcher>
            </Flex>
            {season ? (
                <SeasonDetail season={season} year={year} animeList={animeList}/>
            ) : (
                <YearOverview year={year} seasonList={seasonList} animeList={animeList}/>
            )}
        </StyledPage>
    );
}

function YearOverview({ year, seasonList, animeList }) {
    return seasonList.map((season) => (
        <SeasonPreview key={season} season={season} year={year} animeList={animeList[season]}/>
    ));
}

function SeasonPreview({ season, year, animeList }) {
    return (
        <>
            <Title variant="section">{season}</Title>
            <Flex gapsColumn="1rem">
                {animeList.slice(0, 3).map((anime) => (
                    <AnimeSearchResultCard key={anime.id} anime={anime}/>
                ))}
            </Flex>
            <Flex row justifyContent="center">
                <Button to={`/year/${year}/${season}`} icon>
                    <FontAwesomeIcon icon={faChevronDown} fixedWidth/>
                </Button>
            </Flex>
        </>
    );
}

function SeasonDetail({ season, year, animeList }) {
    return (
        <>
            <Title variant="section">{`Anime from ${season} of ${year}`}</Title>
            <Flex gapsColumn="1rem">
                {animeList.map((anime) => (
                    <AnimeSearchResultCard key={anime.id} anime={anime}/>
                ))}
            </Flex>
        </>
    );
}
