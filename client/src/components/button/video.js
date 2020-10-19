import React from "react";
import {
    faClosedCaptioning,
    faComment,
    faCompactDisc,
    faEye,
    faNotEqual,
    faStream
} from "@fortawesome/free-solid-svg-icons";
import ButtonPlay from "components/button/play";
import Flex from "components/flex";
import Tag from "components/tag";

export default function VideoButton({ video }) {
    return (
        <ButtonPlay to={video.link.replace(".dev", ".moe")}>
            <Flex row wrap gapsBoth="0.75rem">
                <Tag title="Resolution">
                    { video.resolution }p
                </Tag>

                {!!video.nc && (
                    <Tag icon={faNotEqual} title="No Credits"/>
                )}

                {!!video.subbed && (
                    <Tag icon={faClosedCaptioning} title="With Subtitles"/>
                )}

                {!!video.lyrics && (
                    <Tag icon={faComment} title="With Lyrics"/>
                )}

                {!!video.uncen && (
                    <Tag icon={faEye} title="Uncensored"/>
                )}

                {!!video.source && (
                    <Tag icon={faCompactDisc} title="Source">
                        {video.source.toUpperCase()}
                    </Tag>
                )}

                { video.overlap !== "None" && (
                    <Tag icon={faStream} title="Overlap">
                        {video.overlap.toUpperCase()}
                    </Tag>
                ) }
            </Flex>
        </ButtonPlay>
    );
}
