import {useEffect, useState} from "react";
import {fetchAnime, fetchAnimeSlugs} from "../../lib/animeApi";
import ThemeTable from "../../components/themeTable";
import AnimeSynopsis from "../../components/animeSynopsis";
import ExternalLink from "../../components/externalLink";
import {useRouter} from "next/router";
import DescriptionList from "../../components/descriptionList";
import {fetchAniListResources} from "../../lib/aniListApi";

export default function AnimeDetailPage({ anime }) {
    const router = useRouter();

    if (router.isFallback) {
        return <p>Loading...</p>;
    }

    const myAnimeListId = anime.resources[0].link.match(/\d+/)[0];

    const [synopsis, setSynopsis] = useState("Loading...");
    const [image, setImage] = useState(null);

    useEffect(() => {
        fetchAniListResources(myAnimeListId)
            .then(aniListAnime => {
                setSynopsis(aniListAnime.synopsis);
                setImage(aniListAnime.image);
            });
    }, []);

    return (
        <>
            <h1>{anime.name}</h1>
            <div className="anime">
                <div className="anime__sidebar">
                    <img className="anime__cover" src={image} alt="Cover" />
                    <DescriptionList>
                        {{
                            "Alternative Titles": (
                                anime.synonyms.map((synonym) => (
                                    <div key={synonym.text} className="anime__synonym">{synonym.text}</div>
                                ))
                            ),
                            "Premiere": (
                                <>
                                    {!!anime.season && <span>{anime.season + " "}</span>}
                                    <span>{anime.year}</span>
                                </>
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
                </div>
                <main className="anime__main">
                    <h2>Synopsis</h2>
                    <AnimeSynopsis synopsis={synopsis}/>
                    <h2>Themes</h2>
                    <ThemeTable themes={anime.themes} />
                </main>
            </div>
        </>
    );
}

export async function getStaticPaths() {
    const paths = (await fetchAnimeSlugs()).map((slug) => ({
        params: {
            slug: `${slug}`,
        },
    }));

    return {
        paths,
        fallback: true,
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
