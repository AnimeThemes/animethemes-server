console.log("New cache.");

const cache = new Map();

export default function withCache(key, init) {
    if (!cache.has(key)) {
        cache.set(key, init(key));
    }

    return cache.get(key);
}
