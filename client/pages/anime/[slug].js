import {fetchAnime} from "../../lib/animeApi";
import ThemeTable from "../../components/themeTable";
import AnimeSynopsis from "../../components/animeSynopsis";
import ExternalLink from "../../components/externalLink";
import {useRouter} from "next/router";
import DescriptionList from "../../components/descriptionList";
import SidebarView from "../../components/sidebarView";
import {fullWidth} from "../../styles/utils/helper";
import styled from "styled-components";
import {gapsColumn} from "../../styles/utils/gaps";
import useAniList from "../../hooks/useAniList";
import {StyledText, StyledTitlePage, StyledTitleSection} from "../../components/layout/text.styled";

const StyledAnimePage = styled.div`
    ${gapsColumn("1.5rem")}
`;
const StyledCover = styled.img(fullWidth);
const StyledList = styled.div`
    display: flex;
    flex-direction: column;

    ${gapsColumn("0.5rem")}

    text-align: center;
`;

export default function AnimeDetailPage({ anime }) {
    const router = useRouter();

    if (router.isFallback) {
        return <StyledText>Loading...</StyledText>;
    }

    const { synopsis, image } = useAniList(anime);

    return (
        <StyledAnimePage>
            <StyledTitlePage>{anime.name}</StyledTitlePage>
            <SidebarView sidebar={
                <>
                    <StyledCover src={image} alt="Cover"/>
                    <DescriptionList>
                        {{
                            "Alternative Titles": (
                                anime.synonyms.length && (
                                    <StyledList>
                                        {anime.synonyms.map((synonym) => (
                                            <StyledText key={synonym.text}>{synonym.text}</StyledText>
                                        ))}
                                    </StyledList>
                                )
                            ),
                            "Premiere": (
                                <StyledText>
                                    {(anime.season ? anime.season + " " : "") + anime.year}
                                </StyledText>
                            ),
                            "Links": (
                                anime.resources.map((resource) => (
                                    <ExternalLink key={resource.link} href={resource.link}>
                                        {resource.type}
                                    </ExternalLink>
                                ))
                            )
                        }}
                    </DescriptionList>
                </>
            }>
                <StyledTitleSection>Synopsis</StyledTitleSection>
                <AnimeSynopsis synopsis={synopsis}/>
                <StyledTitleSection>Themes</StyledTitleSection>
                <ThemeTable themes={anime.themes} />
            </SidebarView>
        </StyledAnimePage>
    );
}

export async function getStaticPaths() {
    // const paths = (await fetchAnimeSlugs()).map((slug) => ({
    //     params: {
    //         slug: `${slug}`,
    //     },
    // }));
    const paths = [];

    return {
        paths,
        fallback: false,
    };
}

export async function getStaticProps({ params }) {
    const anime = await fetchAnime(params.slug);

    return {
        props: {
            anime,
        },
    };
}
