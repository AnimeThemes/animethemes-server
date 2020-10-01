import {useEffect, useRef, useState} from "react";
import {StyledAnimeSynopsis, StyledCollapsableText} from "./animeSynopsis.styled";
import {StyledCard} from "./layout/card.styled";

function AnimeSynopsis({ synopsis }) {
    const [collapse, setCollapse] = useState(true);
    const [synopsisHeight, setSynopsisHeight] = useState(null);
    const synopsisRef = useRef(null);

    useEffect(() => {
        setSynopsisHeight(synopsisRef.current.offsetHeight);
    }, [collapse, synopsis]);

    return (
        <StyledCard hoverable onClick={() => setCollapse(!collapse)}>
            <StyledAnimeSynopsis style={{ height: `${synopsisHeight}px` }}>
                <StyledCollapsableText collapsed={collapse} ref={synopsisRef} dangerouslySetInnerHTML={{ __html: synopsis }}/>
            </StyledAnimeSynopsis>
        </StyledCard>
    );
}

export default AnimeSynopsis;
