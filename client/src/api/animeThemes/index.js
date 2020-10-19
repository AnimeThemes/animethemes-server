const fetch = require("node-fetch");
const withCache = require("../../utils/withCache");

const baseUrl = "https://animethemes.dev";

function fetchJsonCached(url, init) {
    return withCache(
        url,
        (url) => fetch(url, init).then((response) => response.json())
    );
}

function createFieldParams(fields) {
    return Object.entries(fields)
        .map(([ key, values ]) => `fields[${key}]=${values.join()}`)
        .join("&");
}

module.exports = {
    baseUrl,
    fetchJsonCached,
    createFieldParams
};
