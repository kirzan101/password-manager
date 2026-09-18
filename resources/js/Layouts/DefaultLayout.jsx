import { useEffect, useRef } from "react";
import { useState } from "react";
import {
    Box,
    AppBar as MuiAppBar,
    Toolbar,
    Typography,
    CssBaseline,
    styled,
    useTheme,
    Button,
    Fade,
} from "@mui/material";

import LogoutIcon from "@mui/icons-material/Logout";
import { ThemeButton } from "@/Components";
import { usePage } from "@inertiajs/react";

import GlobalSnackbar from "../Components/Utilities/GlobalSnackbar";
import Logout from "../Components/Pages/Auth/Logout";

const drawerWidth = 245;

/* ================= APP BAR ================= */
const AppBar = styled(MuiAppBar)(({ theme }) => ({
    transition: theme.transitions.create(["margin", "width"], {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
    }),
}));

/* ================= MAIN CONTENT ================= */
const Main = styled("main")(({ theme }) => ({
    flexGrow: 1,
    padding: theme.spacing(3),

    transition: theme.transitions.create("margin", {
        easing: theme.transitions.easing.sharp,
        duration: theme.transitions.duration.leavingScreen,
    }),
}));

/* ================= COMPONENT ================= */
const DefaultLayout = ({ children }) => {
    // app info from backend (via Inertia props)
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";
    const appDeveloper = page.props.appDeveloper || "Developer";
    const appVersion = page.props.appVersion || "1.0.0";

    // logout modal state
    const [logoutOpen, setLogoutOpen] = useState(false);

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

    return (
        <Box sx={{ display: "flex", width: "100%" }}>
            <CssBaseline />

            {/* ================= APP BAR ================= */}
            <AppBar position="fixed" sx={{ backgroundColor: "primary.main" }}>
                <Toolbar variant="dense">
                    <Typography variant="h6" sx={{ flexGrow: 1 }}>
                        {appName}
                    </Typography>

                    <ThemeButton sx={{ mr: 1 }} />

                    <Button
                        color="inherit"
                        variant="text"
                        onClick={() => setLogoutOpen(true)}
                        startIcon={<LogoutIcon />}
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

            {/* ================= MAIN CONTENT ================= */}
            <Main>
                <Fade in={true} timeout={500}>
                    <Box
                        sx={{
                            display: "flex",
                            flexDirection: "column",
                            minHeight: "85vh",
                        }}
                    >
                        <Box component="main" sx={{ flexGrow: 1, pt: 3 }}>
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

export default DefaultLayout;
