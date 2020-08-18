import {useMemo, useState} from "react";
import cn from "classnames";
import ThemeCard from "./themeCard";

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
        <div className="anime__theme-container">
            {groups.length > 1 && (
                <div className="anime__group-tab-container">
                    {groups.map((group, index) => (
                        <button
                            className={cn("button --primary anime__group-tab", {
                                "--active": activeGroup === group,
                            })}
                            onClick={() => setActiveGroup(group)}
                        >
                            {group.name}
                        </button>
                    ))}
                </div>
            )}
            {activeGroup.themes.map((theme, index) => (
                <ThemeCard key={index} theme={theme} />
            ))}
        </div>
    );
}
