import React from 'react';
import {ThemeProvider} from "styled-components";
import {Helmet} from "react-helmet";
import GlobalStyle from "styles/global";
import theme from "theme";
import Navigation from "components/navigation";
import Container from "components/container";

export default function Layout({ children }) {
    return (
        <ThemeProvider theme={theme}>
            <Helmet>
                <meta charSet="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <link href="https://fonts.googleapis.com/css?family=Roboto:wght@400,700&display=swap" rel="stylesheet"/>
                <title>AnimeThemes</title>
            </Helmet>
            <GlobalStyle/>
            <Navigation/>
            <Container>
                {children}
            </Container>
        </ThemeProvider>
    );
}
