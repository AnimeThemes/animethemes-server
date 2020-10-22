const {fetchAnimeList} = require("./src/api/animeThemes/anime");
const seasonOrder = [ "winter", "spring", "summer", "fall" ];

exports.createPages = async ({ actions: { createPage }, reporter }) => {
    const animeList = await fetchAnimeList({ reporter });
    const animeByYear = {};

    for (const anime of animeList) {
        const year = anime.year;
        const season = anime.season.toLowerCase() || null;

        if (!season) {
            continue;
        }

        if (!animeByYear[year]) {
            animeByYear[year] = {};
        }
        if (!animeByYear[year][season]) {
            animeByYear[year][season] = [];
        }

        animeByYear[year][season].push(anime);
    }

    const yearList = Object.keys(animeByYear).sort((a, b) => a - b);

    // Anime pages
    animeList.forEach((anime) => {
        createPage({
            path: `/anime/${anime.alias}`,
            component: require.resolve("./src/templates/anime.js"),
            context: { anime },
        });

        // Video pages
        anime.themes.forEach((theme) => theme.entries.forEach((entry) => entry.videos.forEach((video) => {
            createPage({
                path: `/video/${video.filename}`,
                component: require.resolve("./src/templates/video.js"),
                context: { video, anime, entry, theme }
            });
        })));
    });

    // Year pages
    createPage({
        path: "/year",
        component: require.resolve("./src/templates/year.js"),
        context: { yearList }
    });
    Object.entries(animeByYear).forEach(([year, seasons]) => {
        const seasonList = Object.keys(seasons).sort((a, b) => seasonOrder.indexOf(a) - seasonOrder.indexOf(b));
        createPage({
            path: `/year/${year}`,
            component: require.resolve("./src/templates/season.js"),
            context: { animeList: seasons, year, yearList, seasonList },
        });
        Object.entries(seasons).forEach(([season, animeList]) => {
            createPage({
                path: `/year/${year}/${season}`,
                component: require.resolve("./src/templates/season.js"),
                context: { animeList, year, season, yearList, seasonList },
            });
        })
    });
};
