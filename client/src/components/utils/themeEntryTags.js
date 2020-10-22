import Tag from "components/tag";
import {faBomb, faExclamationTriangle, faFilm} from "@fortawesome/free-solid-svg-icons";
import Flex from "components/flex";
import React from "react";

export default function ThemeEntryTags({ entry }) {
    return (
        <Flex row wrap gapsBoth="0.75rem">
            <Tag icon={faFilm}>
                {entry.episodes || "—"}
            </Tag>
            {!!entry.spoiler && (
                <Tag icon={faBomb} warning>
                    SPOILER
                </Tag>
            )}
            {!!entry.nsfw && (
                <Tag icon={faExclamationTriangle} warning>
                    NSFW
                </Tag>
            )}
        </Flex>
    );
}
