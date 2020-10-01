export default function fetchAnimeThemes(path) {
    return fetch(`https://animethemes.dev${path}`).then((response) => response.json());
}
