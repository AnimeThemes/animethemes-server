import Link from "next/link";
import {fetchAnimeSlugs} from "../../lib/animeApi";

export default function AnimeIndexPage({ animeSlugs }) {
    console.log(animeSlugs)
    return (
        <>
            <h1>Listing all anime</h1>
            {animeSlugs.map((slug) => (
                <Link key={slug} href="/anime/[slug]" as={`/anime/${slug}`}>{slug}</Link>
            ))}
        </>
    );
}

export async function getStaticProps() {
    const animeSlugs = await fetchAnimeSlugs();

    return {
        props: {
            animeSlugs,
        },
    };
}
