import {StyledVideo} from "./videoPlayer.styled";

export default function VideoPlayer({ src }) {
    return (
        <StyledVideo src={src} controls autoPlay>
            Your browser doesn't support HTML5 video playback. Please use a modern browser.
        </StyledVideo>
    )
}
