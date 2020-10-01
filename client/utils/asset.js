import getConfig from "next/config";

const { serverRuntimeConfig } = getConfig();

export default function asset(path) {
    return serverRuntimeConfig.publicFolder + path;
}
