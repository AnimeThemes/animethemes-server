import styled from "styled-components";
import {fetchAnimeByYear} from "../../../lib/animeApi";
import AnimeSearchResultCard from "../../../components/animeSearchResultCard";
import {useRouter} from "next/router";
import {StyledText, StyledTitlePage, StyledTitleSection} from "../../../components/layout/text.styled";
import {StyledGroupTabs} from "../../../components/themeTable.styled";
import {StyledButton} from "../../../components/layout/button.styled";
import {fetchAvailableYears} from "../../../lib/animeApi";
import {gapsColumn, gapsRow} from "../../../styles/utils/gaps";
import Link from "next/link";

const StyledPage = styled.div`
    ${gapsColumn()}
`;
const StyledYearContainer = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;

    ${gapsRow()}
`;
const StyledYearPrevious = styled.div`
    flex: 1;

    display: flex;
    justify-content: flex-end;
`;
const StyledYearNext = styled.div`
    flex: 1;

    display: flex;
    justify-content: flex-start;
`;

const seasonTitles = {
    winter: "Winter",
    spring: "Spring",
    summer: "Summer",
    fall: "Fall",
    other: "Other"
};

export default function SeasonIndexPage({ animeList, year, season, yearList, seasonList }) {
    const router = useRouter();

    if (router.isFallback) {
        return <StyledText>Loading...</StyledText>;
    }

    return (
        <StyledPage>
            <StyledYearContainer>
                <StyledYearPrevious>
                    {yearList.indexOf(+year - 1) >= 0 && (
                        <Link href="/year/[year]/[season]" as={`/year/${+year - 1}/${season}`} passHref>
                            <StyledButton silent>{+year - 1}</StyledButton>
                        </Link>
                    )}
                </StyledYearPrevious>
                <StyledTitlePage>{year}</StyledTitlePage>
                <StyledYearNext>
                    {yearList.indexOf(+year + 1) >= 0 && (
                        <Link href="/year/[year]/[season]" as={`/year/${+year + 1}/${season}`} passHref>
                            <StyledButton silent>{+year + 1}</StyledButton>
                        </Link>
                    )}
                </StyledYearNext>
            </StyledYearContainer>
            <StyledGroupTabs center>
                {seasonList.map((availableSeason) => (
                    <Link href="/year/[year]/[season]" as={`/year/${year}/${availableSeason}`} passHref>
                        <StyledButton active={availableSeason === season}>{seasonTitles[availableSeason]}</StyledButton>
                    </Link>
                ))}
            </StyledGroupTabs>
            <StyledTitleSection>Anime from {season} of {year}</StyledTitleSection>
            <div>
                {animeList.map((anime) => (
                    <AnimeSearchResultCard key={anime.id} anime={anime}/>
                ))}
            </div>
        </StyledPage>
    );
}

export async function getStaticPaths() {
    const availableYears = await fetchAvailableYears();
    const paths = [];

    for (const year of availableYears) {
        const animeByYear = await fetchAnimeByYear(year);
        if (animeByYear[""]) {
            animeByYear.other = animeByYear[""];
            delete animeByYear[""];
        }
        const seasonList = Object.keys(animeByYear);
        for (const season of seasonList) {
            paths.push({
                params: {
                    year: String(year),
                    season
                }
            })
        }
    }

    return {
        paths,
        fallback: false,
    };
}

export async function getStaticProps({ params }) {
    const { year, season } = params;

    const animeByYear = await fetchAnimeByYear(year);
    if (animeByYear[""]) {
        animeByYear.other = animeByYear[""];
        delete animeByYear[""];
    }

    const animeList = animeByYear[season] || [];
    const yearList = await fetchAvailableYears();
    const seasonList = Object.keys(animeByYear);

    return {
        props: {
            animeList,
            year,
            season,
            yearList,
            seasonList
        },
    };
}
