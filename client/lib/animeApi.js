export function fetchAnime(slug) {
    return fetch(`https://animethemes.dev/api/anime/${slug}`)
        .then((response) => response.json());
}

export function fetchAnimeSlugs() {
    return [];
}
