const ThemeInjection = () => {
    // language=JavaScript
    const injectTheme = `
        (function() {
            const root = document.documentElement;
            const theme = window.localStorage.getItem("theme") || "light";

            root.setAttribute("theme", theme);
        })();
  `;

    return (
        <script
            dangerouslySetInnerHTML={{ __html: injectTheme }}
        />
    );
};
export const onRenderBody = ({ setPreBodyComponents }) => {
    setPreBodyComponents(<ThemeInjection />);
};
