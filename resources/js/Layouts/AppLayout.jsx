import React, { useEffect, useRef } from "react";
import { useState } from "react";
import {
    Box,
    AppBar as MuiAppBar,
    Toolbar,
    Typography,
    IconButton,
    Drawer,
    CssBaseline,
    Divider,
    List,
    ListItem,
    ListItemButton,
    ListItemText,
    ListItemIcon,
    styled,
    useTheme,
    useMediaQuery,
    Button,
    Fade,
} from "@mui/material";

import MenuIcon from "@mui/icons-material/Menu";
import ChevronLeftIcon from "@mui/icons-material/ChevronLeft";
import LogoutIcon from "@mui/icons-material/Logout";

import { iconMap } from "@/Utilities/icons";

import { ThemeButton } from "@/Components";
import { router, usePage } from "@inertiajs/react";

import ProfileNav from "./Components/ProfileNav";
import SidebarItem from "./Components/SidebarItem";
import GlobalSnackbar from "../Components/Utilities/GlobalSnackbar";
import Logout from "../Components/Pages/Auth/Logout";
import { CIconButton } from "@/Components";

const drawerWidth = 245;

/* ================= APP BAR ================= */
const AppBar = styled(MuiAppBar, {
    shouldForwardProp: (prop) => prop !== "open" && prop !== "isMobile",
})(({ theme, open, isMobile }) => ({
    transition: theme.transitions.create(["margin", "width"], {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
    }),

    // Only shrink on desktop
    ...(!isMobile &&
        open && {
            width: `calc(100% - ${drawerWidth}px)`,
            marginLeft: `${drawerWidth}px`,

            transition: theme.transitions.create(["margin", "width"], {
                easing: theme.transitions.easing.easeOut,

                duration: theme.transitions.duration.enteringScreen,
            }),
        }),
}));

/* ================= MAIN CONTENT ================= */
const Main = styled("main", {
    shouldForwardProp: (prop) => prop !== "open" && prop !== "isMobile",
})(({ theme, open, isMobile }) => ({
    flexGrow: 1,
    padding: theme.spacing(3),

    transition: theme.transitions.create("margin", {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
    }),

    // Mobile should NEVER shift
    marginLeft: isMobile ? 0 : open ? 0 : `-${drawerWidth}px`,

    ...(!isMobile &&
        open && {
            transition: theme.transitions.create("margin", {
                easing: theme.transitions.easing.easeOut,
                duration: theme.transitions.duration.enteringScreen,
            }),

            marginLeft: 0,
        }),
}));

/* ================= DRAWER HEADER ================= */
const DrawerHeader = styled("div")(({ theme }) => ({
    display: "flex",
    alignItems: "center",
    justifyContent: "flex-end",
    padding: theme.spacing(0, 1),
    // ...theme.mixins.toolbar,
    minHeight: 48, // use to match the height of the AppBar (48px for dense)
}));

/* ================= COMPONENT ================= */
const AppLayout = ({ children }) => {
    const theme = useTheme();
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    // Get user data from Inertia page props
    const user = usePage().props.auth?.user || {};

    // app info from backend (via Inertia props)
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";
    const appDeveloper = page.props.appDeveloper || "Developer";
    const appVersion = page.props.appVersion || "1.0.0";

    // Modules for sidebar - from Inertia props (shared via HandleInertiaRequests middleware)
    const modules = page.props.modules || [];
    const accessibleModules = page.props.auth?.user?.accessibleModules || [];

    // All modules that the user has access to, sorted by order
    const moduleLists = modules
        .filter((module) => accessibleModules.includes(module.base_name)) // Filter modules based on user permissions
        .sort((a, b) => a.order - b.order)
        .map((module) => ({
            name: module.name,
            icon: module.icon,
            route: module.route,
            category: module.category,
        }));

    // Modules that are not categorized (no category field)
    const uncategorizedModules = moduleLists.filter(
        (module) => !module.category,
    );
    const settingsModules = moduleLists.filter(
        (module) => module.category === "settings",
    );

    // Responsive drawer behavior
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    // logout modal state
    const [logoutOpen, setLogoutOpen] = useState(false);

    const handleDrawerOpen = () => setOpen(true);
    const handleDrawerClose = () => setOpen(false);

    // Snackbar logic
    const snackRef = useRef();

    // Listen for flash messages from Inertia and show snackbar
    const { flash } = usePage().props;
    useEffect(() => {
        if (flash?.success) {
            snackRef.current?.show(flash.success, "success");
        }

        if (flash?.error) {
            snackRef.current?.show(flash.error, "error");
        }

        if (flash?.info) {
            snackRef.current?.show(flash.info, "info");
        }

        if (flash?.warning) {
            snackRef.current?.show(flash.warning, "warning");
        }
    }, [flash]);

    // validation errors from backend (via Inertia props)
    const { errors } = usePage().props;
    useEffect(() => {
        if (Object.keys(errors || {}).length > 0) {
            snackRef.current?.show("Validation error occurred", "error");
        }
    }, [errors]);
    // End of Snackbar logic

    // mobile keyboard behavior start
    const [keyboardOpen, setKeyboardOpen] = useState(false);

    useEffect(() => {
        const viewport = window.visualViewport;

        if (!viewport) return;

        const handleResize = () => {
            const keyboardHeight = window.innerHeight - viewport.height;

            setKeyboardOpen(keyboardHeight > 150);
        };

        viewport.addEventListener("resize", handleResize);

        return () => {
            viewport.removeEventListener("resize", handleResize);
        };
    }, []);
    // mobile keyboard behavior end

    return (
        <Box sx={{ display: "flex", width: "100%" }}>
            <CssBaseline />

            {/* ================= APP BAR ================= */}
            <AppBar
                position="fixed"
                open={open}
                isMobile={isMobile}
                sx={{ backgroundColor: "primary.main" }}
            >
                <Toolbar variant="dense">
                    {/* OPEN BUTTON */}
                    <IconButton
                        color="inherit"
                        onClick={handleDrawerOpen}
                        edge="start"
                        sx={{ mr: 2, ...(open && { display: "none" }) }}
                    >
                        <MenuIcon />
                    </IconButton>

                    <Typography variant="h6" sx={{ flexGrow: 1 }}>
                        {appName}
                    </Typography>

                    <ThemeButton sx={{ mr: 1 }} />

                    <Button
                        color="inherit"
                        variant="text"
                        onClick={() => setLogoutOpen(true)}
                        startIcon={<LogoutIcon />}
                        sx={{
                            transition: "all 0.2s ease",
                            "& .MuiButton-startIcon": {
                                transition: "transform 0.2s ease",
                            },

                            "&:hover .MuiButton-startIcon": {
                                transform: "scale(1.15) rotate(-5deg)",
                            },
                        }}
                    >
                        Logout
                    </Button>
                    <Logout
                        open={logoutOpen}
                        onClose={() => setLogoutOpen(false)}
                    />
                </Toolbar>

                {/* Environment warning for UAT environment */}
                {import.meta.env.VITE_APP_ENV === "uat" && (
                    <Box
                        sx={{
                            bgcolor: "#ff9800",
                            color: "#fff",
                            py: 0.25,
                            textAlign: "center",
                            fontSize: 10,
                            fontWeight: 700,
                            letterSpacing: 1,
                        }}
                    >
                        UAT ENVIRONMENT
                    </Box>
                )}
            </AppBar>

            {/* ================= DRAWER ================= */}
            <Drawer
                variant={isMobile ? "temporary" : "persistent"}
                anchor="left"
                open={open}
                onClose={() => setOpen(false)}
                sx={{
                    width: drawerWidth,
                    flexShrink: 0,
                    "& .MuiDrawer-paper": {
                        width: drawerWidth,
                        boxSizing: "border-box",
                    },
                }}
            >
                <DrawerHeader>
                    <IconButton onClick={handleDrawerClose}>
                        {theme.direction === "ltr" ? (
                            <ChevronLeftIcon />
                        ) : (
                            <ChevronLeftIcon />
                        )}
                    </IconButton>
                </DrawerHeader>

                <Divider />

                {/* Profile details */}
                <ProfileNav user={user} />

                <Divider />

                <List>
                    <SidebarItem
                        href="/dashboard"
                        icon={iconMap.DashboardIcon}
                        label="Dashboard"
                    />

                    {uncategorizedModules.map((module) => (
                        <SidebarItem
                            key={module.name}
                            href={`${module.route}`}
                            icon={
                                iconMap[module.icon] || iconMap.ViewModuleIcon
                            }
                            label={module.name.replace(/-/g, " ")}
                        />
                    ))}

                    {settingsModules.length > 0 && (
                        <SidebarItem
                            href="/settings"
                            icon={iconMap.SettingsIcon}
                            label="Settings"
                        />
                    )}

                    {/* UNDER DEVELOPMENT */}
                    {/* <SidebarItem
                        href="/profile"
                        icon={iconMap.AccountCircleIcon}
                        label="Profile"
                    /> */}
                </List>
            </Drawer>

            {/* ================= MAIN CONTENT ================= */}
            <Main
                sx={{
                    width: open ? `calc(100% - ${drawerWidth}px)` : "100%",
                }}
                open={open}
                isMobile={isMobile}
            >
                <DrawerHeader />

                <Fade in={true} timeout={500}>
                    <Box
                        sx={{
                            display: "flex",
                            flexDirection: "column",
                            minHeight: "85vh",
                        }}
                    >
                        <Box component="main" sx={{ flexGrow: 1 }}>
                            {children}
                        </Box>

                        <Box
                            component="footer"
                            sx={{
                                py: 3,
                                px: 2,
                                backgroundColor: "inherit",
                                flexShrink: 0,
                                textAlign: "center",
                            }}
                        >
                            {appName} <br />
                            {appDeveloper} {new Date().getFullYear()} &copy; — v
                            {appVersion}
                        </Box>
                    </Box>
                </Fade>
                <GlobalSnackbar ref={snackRef} />
            </Main>
        </Box>
    );
};

export default AppLayout;
