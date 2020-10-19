import React, {useEffect, useRef, useState} from "react";
import styled from "styled-components";
import Card from "components/card";

const StyledCollapse = styled.div`
    height: ${(props) => props.height};
    overflow: hidden;
    transition: height 250ms;
`;

export default function CollapseCard({ children, ...props }) {
    const [collapse, setCollapse] = useState(true);
    const [height, setHeight] = useState(null);
    const ref = useRef(null);

    useEffect(() => {
        setHeight(ref.current.children[0].offsetHeight);
    }, [collapse, children]);

    return (
        <Card hoverable onClick={() => setCollapse(!collapse)} {...props}>
            <StyledCollapse ref={ref} height={height ? `${height}px` : "auto"}>
                {children(collapse)}
            </StyledCollapse>
        </Card>
    );
}
