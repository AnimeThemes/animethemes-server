export default {
    colors: {
        primaryBackground: [
            createColor("--primary-background-0", {
                light: "hsl(267, 10%, 100%)",
                dark: "hsl(267, 10%, 7%)"
            }),
            createColor("--primary-background-1", {
                light: "hsl(267, 10%, 93%)",
                dark: "hsl(267, 10%, 14%)"
            }),
            createColor("--primary-background-2", {
                light: "hsl(267, 10%, 86%)",
                dark: "hsl(267, 10%, 21%)"
            }),
            createColor("--primary-background-3", {
                light: "hsl(267, 10%, 79%)",
                dark: "hsl(267, 10%, 28%)"
            })
        ],
        primaryTitle: createColor("--primary-title", {
            light: "hsl(0, 0%, 0%)",
            dark: "hsl(0, 0%, 100%)"
        }),
        primaryHighEmphasis: createColor("--primary-high-emphasis", {
            light: "hsla(0, 0%, 0%, 0.87)",
            dark: "hsla(0, 0%, 100%, 0.87)"
        }),
        primaryMediumEmphasis: createColor("--primary-medium-emphasis", {
            light: "hsla(0, 0%, 0%, 0.6)",
            dark: "hsla(0, 0%, 100%, 0.6)"
        }),
        primaryLowEmphasis: createColor("--primary-low-emphasis", {
            light: "hsla(0, 0%, 0%, 0.38)",
            dark: "hsla(0, 0%, 100%, 0.38)"
        }),
        secondaryBackground: createColor("--secondary-background", {
            light: "rgb(0,137,123)",
            dark: "rgb(128, 203, 196)"
        }),
        secondaryTitle: createColor("--secondary-title", {
            light: "rgb(0,137,123)",
            dark: "rgb(128, 203, 196)"
        }),
        warningTitle: createColor("--warning-title", {
            light: "rgb(176,0,32)",
            dark: "rgb(207, 102, 121)"
        })
    },
    elevation: 1,
    createColorDefinition(theme, colorTheme) {
        const css = {};
        for (const color of Object.values(theme.colors).flat()) {
            css[color.name] = color.mappings[colorTheme];
        }
        return css;
    }
};

function createColor(name, mappings) {
    return {
        name,
        mappings,
        toString() {
            return `var(${this.name})`;
        }
    };
}

export function createColorDefinition(theme, colorTheme) {
    const css = {};
    for (const color of Object.values(theme.colors).flat()) {
        css[color.name] = color.mappings[colorTheme];
    }
    return css;
}
