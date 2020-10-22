import React from "react";
import styled from "styled-components";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faPlay} from "@fortawesome/free-solid-svg-icons";
import Button from "components/button";

const StyledButtonPlay = styled(Button)`
    display: flex;
    flex-direction: row;
    align-items: center;

    margin-left: 1rem;
    padding: 0 0.5rem 0 0;
`;
const StyledPrefix = styled.div`
    display: inline-flex;
    justify-content: center;
    align-items: center;

    width: 2rem;
    height: 2rem;
    margin-left: -1rem;
    border-radius: 1rem;

    background-color: ${(props) => props.theme.colors.secondaryBackground};
    color: ${(props) => props.theme.colors.primaryBackground[0]};
`;

export default function ButtonPlay({ children, ...props }) {
    return (
        <StyledButtonPlay {...props}>
            <StyledPrefix>
                <FontAwesomeIcon icon={faPlay} fixedWidth />
            </StyledPrefix>
            {children}
        </StyledButtonPlay>
    );
}
