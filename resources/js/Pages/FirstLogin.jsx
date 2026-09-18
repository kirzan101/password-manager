import {
    Typography,
    Grid,
    TextField,
    Button,
    Box,
    Divider,
} from "@mui/material";
import { CTextField, CButton, CAlertError, CPasswordField } from "@/Components";
import { useTheme, useMediaQuery } from "@mui/material";
import { useState } from "react";
import { router, Head, usePage } from "@inertiajs/react";
import LogoutIcon from "@mui/icons-material/Logout";
import Logout from "@/Components/Pages/Auth/Logout";

import EmptyLayout from "@/Layouts/EmptyLayout";
import UserAvatar from "@/Components/Utilities/UserAvatar";

const FirstLogin = ({ flash, errors }) => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    // Get user data from Inertia page props
    const user = usePage().props.auth?.user || {};

    // app info from backend (via Inertia props)
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";
    const appDeveloper = page.props.appDeveloper || "Developer";
    const appVersion = page.props.appVersion || "1.0.0";

    const [btnDisabled, setBtnDisabled] = useState(false);
    const [form, setForm] = useState({
        password: "",
        password_confirmation: "",
    });

    const isDark = theme.palette.mode === "dark";
    const imageUrl = isDark
        ? "/images/change_password_dark.svg"
        : "/images/change_password_light.svg";

    const handleChange = (field) => (e) => {
        setForm((prev) => ({
            ...prev,
            [field]: e.target.value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault(); // Prevent default form submission behavior

        router.post("/first-login-change-password", form, {
            onSuccess: ({ props }) => {
                // Handle successful login, e.g., redirect or show a success message
            },
            onError: () => {
                // Handle login error, e.g., show an error message
            },
            onBefore: () => {
                setBtnDisabled(true);
            },
            onFinish: () => {
                setBtnDisabled(false);
            },
        });
    };

    // logout modal state
    const [logoutOpen, setLogoutOpen] = useState(false);

    return (
        <>
            <Head title={`First Login — ${appName}`} />
            <Grid container sx={{ minHeight: "100vh" }}>
                {/* LEFT */}
                <Grid
                    size={{ xs: 0, md: 6 }}
                    sx={{
                        display: { xs: "none", md: "flex" },
                        background: (theme) => theme.gradients.primary,
                        color: "white",
                        alignItems: "center",
                        justifyContent: "center",
                        p: 4,
                    }}
                >
                    <Box
                        sx={{
                            textAlign: "center",
                            maxWidth: 500,
                        }}
                    >
                        <img
                            src={imageUrl}
                            alt="First Login"
                            style={{ width: "100%", maxWidth: 400 }}
                        />

                        <Typography
                            sx={{ mt: 4 }}
                            variant="h3"
                            fontWeight="bold"
                        >
                            {appName}
                        </Typography>

                        <Typography variant="h5" sx={{ mt: 2 }}>
                            First Login - Update Your Password
                        </Typography>

                        {/* <Typography variant="body2" sx={{ mt: 3 }}>
                            Optional description or welcome message can be
                            placed here to provide additional context or
                            instructions to the user.
                        </Typography> */}
                    </Box>
                </Grid>

                {/* RIGHT */}
                <Grid
                    size={{ xs: 12, md: 6 }}
                    sx={{
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        bgcolor: (theme) => theme.palette.background,
                        p: 4,
                    }}
                >
                    <Box
                        sx={{
                            width: "100%",
                            maxWidth: 400,
                            display: "flex",
                            flexDirection: "column",
                            alignItems: "center",
                            textAlign: "center",
                        }}
                    >
                        <UserAvatar
                            avatarUrl={user.avatar}
                            initials={user.initials}
                            size={124}
                        />

                        <Typography variant="h4" fontWeight="bold">
                            Welcome {user.nickname || user.first_name}!
                        </Typography>

                        <Typography
                            variant="subtitle1"
                            fontWeight="italic"
                            sx={{ mt: 1 }}
                        >
                            Please Update Your Password
                        </Typography>

                        {errors.error && (
                            <CAlertError sx={{ mt: 2 }}>
                                {errors.error}
                            </CAlertError>
                        )}

                        <form className="mt-2" onSubmit={handleSubmit}>
                            <CPasswordField
                                id="new-password"
                                fullWidth
                                label="New Password"
                                sx={{ mt: 2 }}
                                value={form.password}
                                onChange={handleChange("password")}
                                error={!!errors.password}
                                helperText={errors.password}
                                autoComplete="current-password"
                            />

                            <CPasswordField
                                id="confirm-password"
                                fullWidth
                                label="Confirm Password"
                                sx={{ mt: 2 }}
                                value={form.password_confirmation}
                                onChange={handleChange("password_confirmation")}
                                error={!!errors.password_confirmation}
                                helperText={errors.password_confirmation}
                                autoComplete="current-password"
                            />

                            <CButton
                                fullWidth
                                sx={{
                                    mt: 2,
                                    mx: 0,
                                    background: (theme) =>
                                        theme.palette.gradients.primary,
                                    boxShadow: (theme) =>
                                        theme.palette.shadows.primaryButton,
                                    transition: "all 0.2s ease",

                                    "&:hover": {
                                        background: (theme) =>
                                            theme.palette.gradients
                                                .primaryHover,
                                        transform: "translateY(-2px)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .primaryButtonHover,
                                    },

                                    "&:active": {
                                        transform: "translateY(0)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .primaryButtonActive,
                                    },

                                    "& .MuiButton-startIcon": {
                                        transition: "transform 0.2s ease",
                                    },

                                    "&:hover .MuiButton-startIcon": {
                                        transform: "scale(1.15) rotate(-5deg)",
                                    },
                                }}
                                loading={btnDisabled}
                                loadingPosition="start"
                                type="submit"
                            >
                                UPDATE PASSWORD
                            </CButton>

                            <CButton
                                fullWidth
                                sx={{
                                    mt: 2,
                                    mx: 0,
                                    background: (theme) =>
                                        theme.palette.gradients.error,
                                    boxShadow: (theme) =>
                                        theme.palette.shadows.errorButton,
                                    transition: "all 0.2s ease",

                                    "&:hover": {
                                        background: (theme) =>
                                            theme.palette.gradients.errorHover,
                                        transform: "translateY(-2px)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .errorButtonHover,
                                    },

                                    "&:active": {
                                        transform: "translateY(0)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .errorButtonActive,
                                    },

                                    "& .MuiButton-startIcon": {
                                        transition: "transform 0.2s ease",
                                    },

                                    "&:hover .MuiButton-startIcon": {
                                        transform: "scale(1.15) rotate(-5deg)",
                                    },
                                }}
                                loading={btnDisabled}
                                loadingPosition="start"
                                color="error"
                                onClick={() => setLogoutOpen(true)}
                            >
                                LOGOUT
                            </CButton>
                            <Logout
                                open={logoutOpen}
                                onClose={() => setLogoutOpen(false)}
                            />
                        </form>

                        <Divider sx={{ my: 2 }} />

                        <Typography variant="body2"></Typography>
                        <Typography variant="body2">
                            {appName} <br />
                            {appDeveloper} {new Date().getFullYear()} &copy; — v
                            {appVersion}
                        </Typography>
                    </Box>
                </Grid>
            </Grid>
        </>
    );
};

FirstLogin.layout = (page) => <EmptyLayout>{page}</EmptyLayout>;

export default FirstLogin;
