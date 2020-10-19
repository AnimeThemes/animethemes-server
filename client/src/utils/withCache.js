const cache = new Map();

function withCache(key, init) {
    console.log(key);

    if (!cache.has(key)) {
        cache.set(key, init(key));
    }

    return cache.get(key);
}

module.exports = withCache;
