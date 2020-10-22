const fetch = require("node-fetch");
const withCache = require("../../utils/withCache");

const baseUrl = process.env.GATSBY_API_URL || "https://animethemes.dev";

const requestCooldown = 1000;
let lastRequest;

async function fetchJsonCached(url, init) {
    // Debounce, we should only request once every second
    if (lastRequest) {
        const expiredTime = Date.now() - lastRequest;
        if (expiredTime < requestCooldown) {
            await sleep(requestCooldown - expiredTime);
        }
    }

    return await withCache(
        url,
        (url) => fetch(url, init).then((response) => response.json())
    );
}

function createFieldParams(fields) {
    return Object.entries(fields)
        .map(([ key, values ]) => `fields[${key}]=${values.join()}`)
        .join("&");
}

async function sleep(millis) {
    return await new Promise((resolve) => setTimeout(resolve, millis));
}

module.exports = {
    baseUrl,
    fetchJsonCached,
    createFieldParams
};
