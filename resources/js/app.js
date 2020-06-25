(() => {

    // Declarations

    const url = "https://graphql.anilist.co";

    function fetchAniList() {
        const animeId = document.querySelector("#anime__id").value.match(/\d+/)[0];
        fetchQuery(graphql`
            query($id: Int) {
                Media(idMal: $id, sort: ID) {
                    description
                    coverImage {
                        extraLarge
                    }
                }
            }
        `, {
            id: animeId
        })
            .then(aniListAnime => {
                const synopsis = aniListAnime.Media.description;
                const image = aniListAnime.Media.coverImage.extraLarge;

                document
                    .querySelectorAll(".anime__synopsis-text")
                    .forEach((element) => element.innerHTML = synopsis);
                document
                    .querySelectorAll(".anime__cover")
                    .forEach((element) => element.src = image);
            });
    }

    function fetchQuery(query, variables) {
        return (
            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    query,
                    variables
                })
            })
                .then(response => response.json())
                .then(json => json.data)
        );
    }

    function graphql(strings) {
        return strings.raw[0].trim().replace(/\s+/g, " ");
    }

    function setupCollapseHandler() {
        document.querySelectorAll(".anime__synopsis").forEach(synopsis => {
            let collapsed = synopsis.classList.contains("--collapsed");

            synopsis.addEventListener("click", () => {
                collapsed = !collapsed;

                if (collapsed) {
                    synopsis.classList.add("--collapsed");
                } else {
                    synopsis.classList.remove("--collapsed");
                }
            });
        });
    }

    function setupThemeHandler() {
        let themeDark = false;

        document.querySelectorAll(".theme__switch").forEach(themeSwitch => {
             themeSwitch.addEventListener("click", () => {
                 themeDark = !themeDark;

                 if (themeDark) {
                     document.body.classList.add("--theme-dark");
                 } else {
                     document.body.classList.remove("--theme-dark");
                 }
             });
        });
    }

    function setupThemeGroupHandler() {
        let activeGroup = document.querySelector(".anime__group-tab.--active").getAttribute("data-group");

        onActiveGroupChange(activeGroup);

        document.querySelectorAll(".anime__group-tab").forEach(groupTab => {
            groupTab.addEventListener("click", () => {
                activeGroup = groupTab.getAttribute("data-group");

                // Handle tab active state
                document
                    .querySelectorAll(".anime__group-tab.--active")
                    .forEach(activeGroupTab => activeGroupTab.classList.remove("--active"));
                groupTab.classList.add("--active");

                onActiveGroupChange(activeGroup);
            });
        });

        function onActiveGroupChange(activeGroup) {
            // Handle theme visibility
            document
                .querySelectorAll(".theme-card")
                .forEach(themeCard => {
                    if (themeCard.getAttribute("data-group") === activeGroup) {
                        themeCard.removeAttribute("style");
                    } else {
                        themeCard.setAttribute("style", "display: none;");
                    }
                });
        }
    }

    // Run

    fetchAniList();
    setupCollapseHandler();
    setupThemeHandler();
    setupThemeGroupHandler();

})();
