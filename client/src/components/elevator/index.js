import React from "react";
import {ThemeProvider} from "styled-components";

const elevatedTheme = (add, set) => (theme) => ({
    ...theme,
    elevation: set !== undefined ? set : theme.elevation + add
});

export default function Elevator({ add = 1, set, children }) {
    return (
        <ThemeProvider theme={elevatedTheme(add, set)}>
            {children}
        </ThemeProvider>
    );
}
