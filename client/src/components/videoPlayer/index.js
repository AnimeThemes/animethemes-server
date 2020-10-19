import React from "react";
import styled from "styled-components";
import {StyledVideo} from "./videoPlayer.styled";

const StyledVideo = styled.video`
    width: 100%;
    outline: none;
`;

export default function VideoPlayer({ src, ...props }) {
    return (
        <StyledVideo src={src} controls autoPlay {...props}>
            Your browser doesn't support HTML5 video playback. Please use a modern browser.
        </StyledVideo>
    );
}
