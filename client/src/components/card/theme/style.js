import styled, {css} from "styled-components";
import Flex from "components/flex";
import Text from "components/text";
import {gapsColumn} from "styles/mixins";
import Card from "components/card";

export const StyledThemeCard = styled(Card)`
    ${gapsColumn()}
`;

export const StyledRow = styled.div`
    display: flex;
    flex-direction: row;
    align-items: center;
`;

export const StyledSequence = styled(Text)`
    flex: 0 0 2.5rem;

    display: flex;

    color: ${(props) => props.theme.colors.primaryTitle};

    ${(props) => props.secondary && css`
        color: ${(props) => props.theme.colors.primaryMediumEmphasis};

        @media (max-width: 720px) {
            align-self: flex-start;
        }
    `}
`;

export const StyledHeader = styled.div`
    flex: 1;
`;

export const StyledVersion = styled.div`
    flex: 1;

    display: flex;
    flex-direction: row;
    align-items: center;

    @media (max-width: 720px) {
        flex-direction: column;
        align-items: flex-start;

        ${gapsColumn()}
    }
`;

export const StyledVideoListContainer = styled.div`
    flex: 1;
`;

export const StyledVideoList = styled(Flex).attrs({
    row: true,
    wrap: true,
    gapsBoth: "0.75rem"
})`
    @media (min-width: 721px) {
        justify-content: flex-end;
    }
`;
