import React from "react";
import {Link} from "gatsby";
import styled from "styled-components";
import useAniList from "../hooks/useAniList";
import ExternalLink from "../components/externalLink";
import DescriptionList from "components/descriptionList";
import Text from "components/text";
import Title from "components/text/title";
import Flex from "components/flex";
import ContainerSidebar from "components/container/sidebar";
import CollapseCard from "components/card/collapse";
import {fullWidth, gapsColumn} from "styles/mixins";
import ThemeSwitcher from "components/switcher/theme";

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

export default function AnimeDetailPage({ pageContext: { anime } }) {
    const { synopsis, image } = useAniList(anime);

    const sidebar = (
        <Flex gapsColumn="1.5rem">
            <StyledCover src={image} alt="Cover"/>
            <DescriptionList>
                {{
                    "Alternative Titles": (
                        !!anime.synonyms && !!anime.synonyms.length && (
                            <StyledList>
                                {anime.synonyms.map((synonym) => (
                                    <Text key={synonym.text}>{synonym.text}</Text>
                                ))}
                            </StyledList>
                        )
                    ),
                    "Premiere": (
                        <Link to={`/year/${anime.year}${anime.season ? `/${anime.season.toLowerCase()}` : ""}`}>
                            <Text link>
                                {(anime.season ? anime.season + " " : "") + anime.year}
                            </Text>
                        </Link>
                    ),
                    "Links": (
                        !!anime.resources && anime.resources.map((resource) => (
                            <ExternalLink key={resource.link} href={resource.link}>
                                {resource.type}
                            </ExternalLink>
                        ))
                    )
                }}
            </DescriptionList>
        </Flex>
    );

    return (
        <StyledAnimePage>
            <Title>{anime.name}</Title>
            <ContainerSidebar sidebar={sidebar}>
                <Flex gapsColumn="1rem">
                    <Title variant="section">Synopsis</Title>
                    <CollapseCard>
                        {(collapse) => (
                            <Text maxLines={collapse ? 2 : null} dangerouslySetInnerHTML={{ __html: synopsis }}/>
                        )}
                    </CollapseCard>
                    <Title variant="section">Themes</Title>
                    {
                        !!anime.themes && anime.themes.length
                        ? <ThemeSwitcher themes={anime.themes}/>
                        : <Text>There are no themes for this anime.</Text>
                    }
                </Flex>
            </ContainerSidebar>
        </StyledAnimePage>
    );
}
