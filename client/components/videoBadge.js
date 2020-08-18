import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
    faClosedCaptioning,
    faComment,
    faCompactDisc,
    faEye,
    faNotEqual,
    faPlay, faStream
} from "@fortawesome/free-solid-svg-icons";

export default function VideoBadge({ video }) {
    return (
        <a className="video-badge --hoverable" href={video.link}>
            <div className="video-badge__play-badge --secondary --icon">
                <FontAwesomeIcon icon={faPlay} fixedWidth />
            </div>

            <div className="video-badge__tag-list">
                <span className="icon-text" title="Resolution">
                    <small>{ video.resolution }p</small>
                </span>

                { !!video.nc && (
                    <span className="icon-text" title="No Credits">
                        <FontAwesomeIcon icon={faNotEqual} fixedWidth className="icon-text__icon"/>
                    </span>
                ) }

                { !!video.subbed && (
                    <span className="icon-text" title="With Subtitles">
                        <FontAwesomeIcon icon={faClosedCaptioning} fixedWidth className="icon-text__icon"/>
                    </span>
                ) }

                { !!video.lyrics && (
                    <span className="icon-text" title="With Lyrics">
                        <FontAwesomeIcon icon={faComment} fixedWidth className="icon-text__icon"/>
                    </span>
                ) }

                { !!video.uncen && (
                    <span className="icon-text" title="Uncensored">
                        <FontAwesomeIcon icon={faEye} fixedWidth className="icon-text__icon"/>
                    </span>
                ) }

                { !!video.source && (
                    <span className="icon-text" title="Source">
                        <FontAwesomeIcon icon={faCompactDisc} fixedWidth className="icon-text__icon"/>
                        <small>{ video.source.toUpperCase() }</small>
                    </span>
                ) }

                { video.overlap !== "None" && (
                    <span className="icon-text" title="Overlap">
                        <FontAwesomeIcon icon={faStream} fixedWidth className="icon-text__icon"/>
                        <small>{ video.overlap.toUpperCase() }</small>
                    </span>
                ) }
            </div>
        </a>
    );
}
