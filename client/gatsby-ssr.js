const ThemeInjection = () => {
    // language=JavaScript
    const injectTheme = `
        (function() {
            const body = document.body;
            const theme = window.localStorage.getItem("theme");

            if (theme) {
                body.setAttribute("theme", theme);
            }
        })();
    `;

    return (
        <script
            dangerouslySetInnerHTML={{ __html: injectTheme }}
        />
    );
};

export const onRenderBody = ({ setBodyAttributes, setPreBodyComponents }) => {
    setBodyAttributes({ theme: "light" });
    setPreBodyComponents(<ThemeInjection />);
};
