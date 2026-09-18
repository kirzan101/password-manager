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

import EmptyLayout from "@/Layouts/EmptyLayout";

const Login = ({ flash, errors }) => {
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    // app info from backend (via Inertia props)
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";
    const appDeveloper = page.props.appDeveloper || "Developer";
    const appVersion = page.props.appVersion || "1.0.0";

    const [btnDisabled, setBtnDisabled] = useState(false);
    const [form, setForm] = useState({
        username: "",
        password: "",
    });

    const handleChange = (field) => (e) => {
        setForm((prev) => ({
            ...prev,
            [field]: e.target.value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault(); // Prevent default form submission behavior

        router.post("/login", form, {
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

    return (
        <>
            <Head title={`Login — ${appName}`} />
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
                        <Typography variant="h3" fontWeight="bold">
                            Laravel React App
                        </Typography>

                        <Typography variant="h5" sx={{ mt: 2 }}>
                            Subtitle or tagline goes here
                        </Typography>

                        <Typography variant="body2" sx={{ mt: 3 }}>
                            Optional description or welcome message can be
                            placed here to provide additional context or
                            instructions to the user.
                        </Typography>
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
                            textAlign: "center",
                        }}
                    >
                        <Typography variant="h4" fontWeight="bold">
                            Welcome!
                        </Typography>

                        {errors.error && (
                            <CAlertError sx={{ mt: 2 }}>
                                {errors.error}
                            </CAlertError>
                        )}

                        <form className="mt-2" onSubmit={handleSubmit}>
                            <CTextField
                                fullWidth
                                label="Username"
                                sx={{ mt: 2 }}
                                value={form.username}
                                onChange={handleChange("username")}
                                error={!!errors.username}
                                helperText={errors.username}
                                autoComplete="username"
                            />

                            <CPasswordField
                                fullWidth
                                label="Password"
                                sx={{ mt: 2 }}
                                value={form.password}
                                onChange={handleChange("password")}
                                error={!!errors.password}
                                helperText={errors.password}
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
                                LOGIN
                            </CButton>
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

Login.layout = (page) => <EmptyLayout>{page}</EmptyLayout>;

export default Login;
