import {useMemo, useState} from "react";
import ThemeCard from "./themeCard";
import {StyledGroupTabs, StyledThemeTable} from "./themeTable.styled";
import {StyledButton} from "./layout/button.styled";

export default function ThemeTable({ themes }) {
    if (!themes.length) {
        return <span>There are no themes for this anime.</span>;
    }

    const groups = useMemo(() => themes.reduce((groups, theme) => {
        const group = groups.find((group) => group.name === theme.group);
        if (!group) {
            groups.push({
                name: theme.group,
                themes: [theme],
            });
        } else {
            group.themes.push(theme);
        }
        return groups;
    }, []), [ themes ]);

    const [activeGroup, setActiveGroup] = useState(groups[0]);

    return (
        <StyledThemeTable>
            {groups.length > 1 && (
                <StyledGroupTabs>
                    {groups.map((group) => (
                        <StyledButton
                            key={group.name}
                            active={activeGroup === group}
                            onClick={() => setActiveGroup(group)}
                        >
                            {group.name}
                        </StyledButton>
                    ))}
                </StyledGroupTabs>
            )}
            {activeGroup.themes.map((theme, index) => (
                <ThemeCard key={index} theme={theme} />
            ))}
        </StyledThemeTable>
    );
}
