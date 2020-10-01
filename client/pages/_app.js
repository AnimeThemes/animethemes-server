import Head from "next/head";
import {useState} from "react";
import Navigation from "../components/navigation";
import GlobalStyle from "../styles/global";
import lightTheme from "../styles/themes/light";
import darkTheme from "../styles/themes/dark";
import {ThemeProvider} from "styled-components";

// This will make sure FontAwesome's CSS is loaded
import { config } from "@fortawesome/fontawesome-svg-core";
import "@fortawesome/fontawesome-svg-core/styles.css";
import {StyledContainer} from "../components/layout/container.styled";
config.autoAddCss = false;

export default function App({ Component, pageProps }) {
    const [ theme, setTheme ] = useState(lightTheme);

    const toggleTheme = () => setTheme(theme.isDark ? lightTheme : darkTheme);

    return (
        <ThemeProvider theme={theme}>
            <Head>
                <meta charSet="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <link href="https://fonts.googleapis.com/css?family=Roboto:wght@400,700&display=swap" rel="stylesheet"/>
                <title>AnimeThemes</title>
            </Head>
            <GlobalStyle/>
            <Navigation toggleTheme={toggleTheme}/>
            <StyledContainer>
                <Component {...pageProps} />
            </StyledContainer>
        </ThemeProvider>
    );
}
