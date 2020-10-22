import styled from "styled-components";
import ButtonPlay from "components/button/play";
import VideoTags from "components/utils/videoTags";

const StyledBody = styled.div`
    padding: calc(0.75rem / 2);
`;

export default function VideoButton({ video }) {
    return (
        <ButtonPlay to={`/video/${video.filename}`}>
            <StyledBody>
                <VideoTags video={video}/>
            </StyledBody>
        </ButtonPlay>
    );
}
