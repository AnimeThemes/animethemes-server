import VideoBadge from "./videoBadge";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBomb, faExclamationTriangle, faFilm } from "@fortawesome/free-solid-svg-icons";

export default function ThemeCard({ theme }) {
    return (
        <div className="theme-card">
            <div className="theme-card__row">
                <div className="theme-card__sequence">
                    <small>{ theme.slug }</small>
                </div>
                <div className="theme-card__header">
                    <span className="theme-card__title">{ theme.song.title }</span>
                    { !!theme.song.artists.length && (
                        <>
                            <small> by </small>
                            { theme.song.artists.map((artist, index) => (
                                <span key={artist.as || artist.name} className="theme-card__artist">
                                    { artist.as || artist.name}{ index < theme.song.artists.length - 1 ? ', ' : ''}
                                </span>
                            )) }
                        </>
                    ) }
                </div>
            </div>
            { theme.entries.map(entry => (
                <div key={entry.version || 0} className="theme-card__row">
                    <div className="theme-card__sequence --secondary">
                        { !!entry.version && (
                            <small>v{ entry.version }</small>
                        ) }
                    </div>
                    <div className="theme-card__body">
                        <div className="theme-card__tag-list">
                            <div className="icon-text">
                                <FontAwesomeIcon icon={faFilm} className="icon-text__icon"/>
                                <small>{ entry.episodes || "—"}</small>
                            </div>
                            { !!entry.spoiler && (
                                <div className="icon-text">
                                    <FontAwesomeIcon icon={faBomb} className="icon-text__icon --warning"/>
                                    <small>SPOILER</small>
                                </div>
                            ) }
                            { !!entry.nsfw && (
                                <div className="icon-text">
                                    <FontAwesomeIcon icon={faExclamationTriangle} className="icon-text__icon --warning"/>
                                    <small>NSFW</small>
                                </div>
                            ) }
                        </div>
                        <div className="theme-card__video-list">
                            { entry.videos.map((video, index) => (
                                <VideoBadge key={index} video={video}/>
                            )) }
                        </div>
                    </div>
                </div>
            )) }
        </div>
    );
}
