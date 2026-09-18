import { createTheme } from "@mui/material/styles";
import { light, dark } from "./Themes/DefaultTheme";
import { lighten } from "@mui/material/styles";

const buildPalette = (theme) => {
    return {
        mode: theme.mode,

        primary: { main: theme.colors.primary, contrastText: "#fff" },
        primaryAlt1: { main: theme.colors.primaryAlt1, contrastText: "#fff" },
        secondary: { main: theme.colors.secondary, contrastText: "#fff" },
        accent: { main: theme.colors.accent, contrastText: "#fff" },
        error: { main: theme.colors.error },

        textField: { main: theme.colors.textField },
        closeText: { main: theme.colors.closeText },

        background: {
            default: theme.colors.background,
        },

        text: {
            primary: theme.colors.text.primary,
            secondary: theme.colors.text.secondary,
        },

        buttonTextColor: {
            main: theme.colors.buttonTextColor,
        },

        iconColor: { main: theme.colors.iconColor },

        gradients: {
            primary: "linear-gradient(135deg, #13294B 0%, #245B9E 100%)",
            primaryHover: "linear-gradient(135deg, #0B1C3A 0%, #142C55 100%)",

            secondary: "linear-gradient(135deg, #b08521 0%, #d8ad2c 100%)",
            secondaryHover: "linear-gradient(135deg, #966f16 0%, #c7960c 100%)",

            accent: "linear-gradient(135deg, #185D33 0%, #2E8B57 100%)",
            accentHover: "linear-gradient(135deg, #124726 0%, #236B42 100%)",

            error:
                theme.mode === "dark"
                    ? "linear-gradient(135deg, #b71c1c 0%, #F44336 100%)"
                    : "linear-gradient(135deg, #8B1515 0%, #b71c1c 100%)",

            errorHover:
                theme.mode === "dark"
                    ? "linear-gradient(135deg, #c62828 0%, #ff5c5c 100%)"
                    : "linear-gradient(135deg, #a01919 0%, #d32f2f 100%)",
        },

        shadows: {
            primaryButton: "0 4px 12px rgba(19, 41, 75, 0.3)",
            primaryButtonHover: "0 7px 18px rgba(19, 41, 75, 0.4)",
            primaryButtonActive: "0 3px 8px rgba(19, 41, 75, 0.25)",

            accentButton: "0 4px 12px rgba(24, 93, 51, 0.3)",
            accentButtonHover: "0 7px 18px rgba(24, 93, 51, 0.4)",
            accentButtonActive: "0 3px 8px rgba(24, 93, 51, 0.25)",

            secondaryButton: "0 4px 12px rgba(199, 150, 12, 0.3)",
            secondaryButtonHover: "0 7px 18px rgba(199, 150, 12, 0.4)",
            secondaryButtonActive: "0 3px 8px rgba(199, 150, 12, 0.25)",

            errorButton: "0 4px 12px rgba(183, 28, 28, 0.3)",
            errorButtonHover: "0 7px 18px rgba(183, 28, 28, 0.4)",
            errorButtonActive: "0 3px 8px rgba(183, 28, 28, 0.25)",
        },
    };
};

export const getTheme = (mode = "light") => {
    const isDark = mode === "dark";
    const themeConfig = isDark ? dark : light;

    return createTheme({
        palette: buildPalette(themeConfig),

        gradients: {
            primary: "linear-gradient(135deg, #0B1C3A 0%, #142C55 100%)", // used in login/first login page
        },

        breakpoints: {
            values: {
                xs: 0,
                sm: 600,
                md: 960,
                lg: 1280,
                xl: 1920,
            },
        },

        components: {
            MuiCssBaseline: {
                styleOverrides: (theme) => ({
                    body: {
                        backgroundColor: theme.palette.background.default,
                        color: theme.palette.text.primary,
                    },
                }),
            },

            MuiAppBar: {
                styleOverrides: {
                    root: {
                        backgroundImage: "none",
                    },
                },
            },

            MuiButton: {
                defaultProps: {
                    variant: "contained",
                },
                styleOverrides: {
                    root: {
                        textTransform: "none",
                        borderRadius: 8,
                    },
                },
            },

            MuiTextField: {
                defaultProps: {
                    variant: "outlined",
                },
            },

            // =====================================================
            // ✅ DARK MODE ONLY OVERRIDES (DataGrid + Inputs fix)
            // =====================================================
            ...(isDark && {
                MuiCheckbox: {
                    styleOverrides: {
                        root: {
                            color: themeConfig.colors.textField,

                            "&.Mui-checked": {
                                color: themeConfig.colors.textField,
                            },

                            "&.MuiCheckbox-indeterminate": {
                                color: themeConfig.colors.textField,
                            },
                        },
                    },
                },

                MuiOutlinedInput: {
                    styleOverrides: {
                        root: {
                            "&.Mui-focused .MuiOutlinedInput-notchedOutline": {
                                borderColor: themeConfig.colors.textField,
                            },
                        },
                    },
                },
            }),

            // =====================================================
            // ✅ OVERRIDES FOR DATAGRID (FOCUS + HOVER)
            // =====================================================
            MuiDataGrid: {
                styleOverrides: {
                    root: {
                        "& .MuiDataGrid-cell:focus, & .MuiDataGrid-cell:focus-within":
                            {
                                outline: "none",
                                boxShadow: "none",
                            },
                        "& .MuiDataGrid-columnHeader:focus, & .MuiDataGrid-columnHeader:focus-within":
                            {
                                outline: "none",
                                boxShadow: "none",
                            },
                    },
                },
            },

            // =====================================================
            // ✅ START OVERRIDES FOR TABS (LEFT SIDEBAR)
            // =====================================================
            MuiTabs: {
                styleOverrides: {
                    root: ({ theme }) => ({
                        borderRight: `1px solid ${theme.palette.divider}`,
                        minWidth: 220,
                        paddingRight: theme.spacing(1),

                        "& .MuiTabs-indicator": {
                            display: "none",
                        },
                    }),
                },
            },

            MuiTab: {
                styleOverrides: {
                    root: ({ theme }) => ({
                        justifyContent: "flex-start",
                        alignItems: "center",
                        textAlign: "left",
                        textTransform: "none",

                        minHeight: 52,
                        gap: theme.spacing(1.5),

                        margin: theme.spacing(0.5, 1),
                        padding: theme.spacing(1.5, 2),

                        borderRadius: theme.shape.borderRadius * 2,

                        color: theme.palette.text.primary,

                        transition: theme.transitions.create(
                            ["background-color", "color"],
                            {
                                duration: theme.transitions.duration.shortest,
                            },
                        ),

                        "& .MuiSvgIcon-root": {
                            fontSize: 22,
                        },

                        "&.Mui-selected": {
                            backgroundColor: theme.palette.primary.main,
                            color: theme.palette.primary.contrastText,

                            "& .MuiSvgIcon-root": {
                                color: theme.palette.primary.contrastText,
                            },
                        },

                        "&:hover": {
                            backgroundColor: lighten(
                                theme.palette.primary.main,
                                0.2,
                            ),
                        },
                    }),
                },
            },
            // =====================================================
            // ✅ END OVERRIDES FOR TABS (LEFT SIDEBAR)
            // =====================================================

            // =====================================================
            // ✅ OVERRIDES FOR TEXTFIELDS
            // =====================================================
            MuiTextField: {
                styleOverrides: {
                    root: ({ theme }) => ({
                        '& input[type="date"]::-webkit-calendar-picker-indicator':
                            {
                                filter:
                                    theme.palette.mode === "dark"
                                        ? "invert(1)"
                                        : "none",
                            },
                    }),
                },
            },
            // =====================================================
            // ✅ END OVERRIDES FOR TEXTFIELDS
            // =====================================================
        },

        transitions: {
            duration: {
                enteringScreen: 400,
                leavingScreen: 300,
            },
        },
    });
};
