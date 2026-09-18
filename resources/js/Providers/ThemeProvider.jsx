import React, { useEffect, useMemo, useState } from "react";
import { ThemeProvider as MuiThemeProvider, CssBaseline } from "@mui/material";

import { ThemeContext } from "@/Contexts/ThemeContext";
import { getTheme } from "@/mui";

export const ThemeProvider = ({ children }) => {
    // ================= SYSTEM DETECTION =================
    const getSystemTheme = () =>
        window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";

    const getInitialTheme = () => {
        const saved = localStorage.getItem("theme-mode");
        return saved || getSystemTheme();
    };

    const [mode, setMode] = useState(getInitialTheme);

    // ================= TOGGLE =================
    const toggleTheme = () => {
        setMode((prev) => (prev === "light" ? "dark" : "light"));
    };

    const changeMode = (newMode) => setMode(newMode);

    // ================= PERSIST =================
    useEffect(() => {
        localStorage.setItem("theme-mode", mode);
    }, [mode]);

    // ================= SYSTEM LISTENER =================
    useEffect(() => {
        const media = window.matchMedia("(prefers-color-scheme: dark)");

        const handler = (e) => {
            const saved = localStorage.getItem("theme-mode");
            if (!saved) {
                setMode(e.matches ? "dark" : "light");
            }
        };

        media.addEventListener("change", handler);
        return () => media.removeEventListener("change", handler);
    }, []);

    // ================= MUI THEME =================
    const theme = useMemo(() => getTheme(mode), [mode]);

    // ================= CONTEXT VALUE =================
    const value = useMemo(
        () => ({
            mode,
            setMode: changeMode,
            toggleTheme,
        }),
        [mode],
    );

    return (
        <ThemeContext.Provider value={value}>
            <MuiThemeProvider theme={theme}>
                <CssBaseline />
                {children}
            </MuiThemeProvider>
        </ThemeContext.Provider>
    );
};

export default ThemeProvider;
