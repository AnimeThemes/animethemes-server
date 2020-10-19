import {css} from "styled-components";

export const fullWidth = css`
    width: 100%;
`;

export function gapsRow(gapSize = "1rem") {
    return css`
        & > :not(:first-child) {
            margin: 0 0 0 ${gapSize};
        }
    `;
}

export function gapsColumn(gapSize = "1rem") {
    return css`
        & > :not(:first-child) {
            margin: ${gapSize} 0 0 0;
        }
    `;
}
