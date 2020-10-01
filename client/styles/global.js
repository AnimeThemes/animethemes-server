import {createGlobalStyle} from "styled-components";

export default createGlobalStyle`
    * {
        box-sizing: border-box;
        transition: color, background-color 250ms;
    }

    html {
        overflow-y: scroll;
    }

    body {
        margin: 0;
        min-height: 100vh;

        background-color: ${(props) => props.theme.colors.primaryBackground[0]};

        font-family: "Roboto", sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    a {
        color: inherit;
        text-decoration: inherit;
    }

    button {
        border: none;
        margin: 0;
        padding: 0;
        width: auto;
        overflow: visible;
        background: transparent;
        color: inherit;
        font: inherit;
        text-align: inherit;
        outline: none;
        line-height: inherit;
        -webkit-appearance: none;
    }

    input[type="text"] {
        width: 100%;
        border: none;
        outline: none;
        background-color: transparent;
        color: inherit;
        font-size: inherit;
        font-weight: inherit;

        ::placeholder {
            color: ${(props) => props.theme.colors.primaryMediumEmphasis};
        }
    }
`;
