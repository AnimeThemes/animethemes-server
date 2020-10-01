import React from 'react';
import {StyledDescriptionList, StyledKey, StyledValue} from "./descriptionList.styled";
import {StyledTitleSection} from "./layout/text.styled";

function DescriptionList({ children }) {
    return (
        <StyledDescriptionList>
            {Object.entries(children).map(([ title, description ]) => (
                !!description && (
                    <>
                        <StyledKey>
                            <StyledTitleSection>{title}</StyledTitleSection>
                        </StyledKey>
                        <StyledValue>{description}</StyledValue>
                    </>
                )
            ))}
        </StyledDescriptionList>
    );
}

export default DescriptionList;
