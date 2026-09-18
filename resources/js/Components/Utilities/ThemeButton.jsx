import { IconButton, Tooltip } from "@mui/material";
import DarkModeIcon from "@mui/icons-material/DarkMode";
import LightModeIcon from "@mui/icons-material/LightMode";

import { useThemeMode } from "@/Contexts/ThemeContext";

const ThemeButton = ({ ...props }) => {
    const { mode, toggleTheme } = useThemeMode();

    return (
        <Tooltip title="Toggle Theme" placement="bottom" arrow>
            <IconButton
                aria-label={props["aria-label"] ?? "Toggle Theme"}
                sx={{
                    m: 1,
                    borderRadius: 2,
                    transition: "all 0.2s ease",

                    "& .MuiSvgIcon-root": {
                        transition: "transform 0.2s ease",
                    },

                    "&:hover": {
                        transform: "translateY(-2px)",
                        backgroundColor: "action.hover",
                        boxShadow: (theme) =>
                            `0 5px 14px ${theme.palette.primary.main}20`,
                    },

                    "&:hover .MuiSvgIcon-root": {
                        transform: "scale(1.12) rotate(-5deg)",
                    },

                    "&:active": {
                        transform: "translateY(0)",
                        boxShadow: "none",
                    },

                    "&:focus-visible": {
                        outline: "2px solid",
                        outlineColor: "primary.main",
                        outlineOffset: 2,
                    },
                }}
                onClick={toggleTheme}
                color="inherit"
                {...props}
            >
                {mode === "light" ? <DarkModeIcon /> : <LightModeIcon />}
            </IconButton>
        </Tooltip>
    );
};

export default ThemeButton;
