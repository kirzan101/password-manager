import { useState } from "react";

import {
    Alert,
    Avatar,
    Box,
    Button,
    Chip,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Divider,
    Grid,
    IconButton,
    Snackbar,
    Stack,
    TextField,
    Typography,
} from "@mui/material";

import {
    Badge as BadgeIcon,
    CalendarMonth as CalendarIcon,
    Close as CloseIcon,
    Edit as EditIcon,
    Email as EmailIcon,
    Lock as LockIcon,
    Person as PersonIcon,
    Phone as PhoneIcon,
    Work as WorkIcon,
    Dashboard as DashboardIcon,
} from "@mui/icons-material";

import { CBoxContent, AvatarUpload } from "@/Components";
import AlertTransaction from "@/Components/Utilities/AlertTransaction";
import InfoRow from "./Components/InfoRow";

import { router } from "@inertiajs/react";
import EditProfile from "./Actions/EditProfile";
import ChangePassword from "./Actions/ChangePassword";

const ProfileContent = ({ flash, errors = {}, profile = {} }) => {
    const [editOpen, setEditOpen] = useState(false);
    const [changeOpen, setChangeOpen] = useState(false);

    const [form, setForm] = useState({
        first_name: profile.first_name ?? "",
        middle_name: profile.middle_name ?? "",
        last_name: profile.last_name ?? "",
        nickname: profile.nickname ?? "",
        username: profile.username ?? "",
        email: profile.email ?? "",
        contact_number: profile.contact_numbers?.[0] ?? "",
    });

    const initials =
        profile.initials ||
        profile.full_name
            ?.split(" ")
            .filter(Boolean)
            .map((name) => name.charAt(0))
            .join("")
            .slice(0, 2)
            .toUpperCase() ||
        "U";

    const handleChange = (field) => (event) => {
        setForm((current) => ({
            ...current,
            [field]: event.target.value,
        }));
    };

    const handleSave = () => {
        /*
         * Handle your profile update here.
         *
         * Example:
         *
         * router.put(route("profile.update"), form, {
         *     preserveScroll: true,
         *     onSuccess: () => setEditOpen(false),
         * });
         */

        console.log("Updated profile:", form);
    };

    const handleAvatarChange = (blob) => {
        router.post(
            "/change-avatar",
            {
                _method: "PUT",
                avatar: blob,
            },
            {
                forceFormData: true,
                onSuccess: () => {},
                onError: (errors) => {
                    console.error("Error changing avatar", errors);
                },
            },
        );
    };

    return (
        <CBoxContent>
            {flash?.error ? <AlertTransaction flash={flash} /> : null}

            <Grid
                sx={{
                    display: "flex",
                    justifyContent: "flex-end",
                    overflow: "hidden",
                    border: 1,
                    borderColor: "divider",
                    borderRadius: 3,
                    bgcolor: "background.paper",
                    boxShadow: "0 4px 20px rgba(0, 0, 0, 0.08)",
                    width: "100%",
                    maxWidth: 1000,
                    mx: "auto",
                    p: 1,
                    mb: 2,
                }}
            >
                <Button
                    startIcon={<DashboardIcon />}
                    variant="contained"
                    sx={{
                        px: 2.5,
                        py: 1.1,
                        borderRadius: 2.5,
                        fontWeight: 700,
                        textTransform: "none",
                        letterSpacing: 0.2,
                        background: (theme) => theme.palette.gradients.primary,
                        boxShadow: (theme) =>
                            theme.palette.shadows.primaryButton,
                        transition: "all 0.2s ease",

                        "&:hover": {
                            background: (theme) =>
                                theme.palette.gradients.primary,
                            transform: "translateY(-2px)",
                            boxShadow: (theme) =>
                                theme.palette.shadows.primaryButtonHover,
                        },

                        "&:active": {
                            transform: "translateY(0)",
                            boxShadow: (theme) =>
                                theme.palette.shadows.primaryButtonActive,
                        },

                        "& .MuiButton-startIcon": {
                            transition: "transform 0.2s ease",
                        },

                        "&:hover .MuiButton-startIcon": {
                            transform: "scale(1.15) rotate(-5deg)",
                        },
                    }}
                    onClick={() => router.visit("/dashboard")}
                >
                    Go to Dashboard
                </Button>
            </Grid>

            <Box
                sx={{
                    width: "100%",
                    maxWidth: 1000,
                    mx: "auto",
                }}
            >
                {/* =====================================================
                    PROFILE CARD
                ====================================================== */}
                <Box
                    sx={{
                        overflow: "hidden",
                        border: 1,
                        borderColor: "divider",
                        borderRadius: 3,
                        bgcolor: "background.paper",
                        boxShadow: "0 4px 20px rgba(0, 0, 0, 0.08)",
                    }}
                >
                    {/* =================================================
                        PROFILE BANNER
                    ================================================== */}
                    <Box
                        sx={{
                            position: "relative",
                            height: {
                                xs: 140,
                                sm: 190,
                            },
                            overflow: "hidden",
                            background: (theme) =>
                                `linear-gradient(
                                    135deg,
                                    ${theme.palette.primary.dark} 0%,
                                    ${theme.palette.primary.main} 50%,
                                    ${theme.palette.secondary.main} 100%
                                )`,
                        }}
                    >
                        {/* Decorative circles */}
                        <Box
                            aria-hidden="true"
                            sx={{
                                position: "absolute",
                                width: 240,
                                height: 240,
                                top: -110,
                                right: -40,
                                borderRadius: "50%",
                                bgcolor: "rgba(255,255,255,0.08)",
                            }}
                        />

                        <Box
                            aria-hidden="true"
                            sx={{
                                position: "absolute",
                                width: 160,
                                height: 160,
                                bottom: -90,
                                left: "35%",
                                borderRadius: "50%",
                                bgcolor: "rgba(255,255,255,0.06)",
                            }}
                        />

                        {/* Edit Profile */}
                        <Button
                            variant="contained"
                            startIcon={<EditIcon />}
                            onClick={() => setEditOpen(true)}
                            sx={{
                                position: "absolute",
                                top: {
                                    xs: 12,
                                    sm: 20,
                                },
                                right: {
                                    xs: 12,
                                    sm: 20,
                                },
                                color: "#fff",
                                bgcolor: "rgba(0, 0, 0, 0.35)",
                                backdropFilter: "blur(8px)",
                                "&:hover": {
                                    bgcolor: "rgba(0, 0, 0, 0.55)",
                                },
                            }}
                        >
                            Edit Profile
                        </Button>
                    </Box>

                    {/* =================================================
                        PROFILE HEADER
                    ================================================== */}
                    <Box
                        sx={{
                            position: "relative",
                            px: {
                                xs: 2,
                                sm: 4,
                            },
                            pb: 4,
                        }}
                    >
                        {/* Avatar */}
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: {
                                    xs: "center",
                                    sm: "flex-start",
                                },
                            }}
                        >
                            <Box
                                sx={{
                                    position: "relative",
                                    mt: -7,
                                }}
                            >
                                {/* <Avatar
                                    src={profile.avatar || undefined}
                                    alt={profile.full_name || "Profile avatar"}
                                    sx={{
                                        width: {
                                            xs: 110,
                                            sm: 140,
                                        },
                                        height: {
                                            xs: 110,
                                            sm: 140,
                                        },
                                        border: "7px solid",
                                        borderColor: "background.paper",
                                        bgcolor: "primary.main",
                                        fontSize: 40,
                                        fontWeight: 700,
                                    }}
                                >
                                    {initials}
                                </Avatar> */}
                                <AvatarUpload
                                    avatarUrl={profile.avatar}
                                    initials={initials}
                                    size={150}
                                    onChange={handleAvatarChange}
                                    avatarSx={{
                                        bgcolor: "primary.main",
                                        fontSize: 40,
                                        fontWeight: 700,

                                        // Separation from the page
                                        border: "4px solid",
                                        borderColor: "background.paper",

                                        // Role ring
                                        outline: "3px solid",
                                        outlineColor: profile.is_admin
                                            ? "secondary.main"
                                            : "primary.main",

                                        outlineOffset: 2,

                                        boxShadow: (theme) =>
                                            profile.is_admin
                                                ? `0 0 0 5px ${theme.palette.secondary.main}20,
                                                   0 0 20px ${theme.palette.secondary.main}45,
                                                   0 8px 24px ${theme.palette.primary.main}30`
                                                : `0 0 0 5px ${theme.palette.primary.main}15,
                                                   0 0 16px ${theme.palette.primary.main}30,
                                                   0 8px 24px ${theme.palette.primary.main}25`,

                                        transition: "all 0.25s ease",

                                        "&:hover": {
                                            transform: "scale(1.03)",

                                            boxShadow: (theme) =>
                                                profile.is_admin
                                                    ? `0 0 0 4px ${theme.palette.secondary.main},
                                                       0 0 28px ${theme.palette.secondary.main}60,
                                                       0 10px 28px ${theme.palette.primary.main}40`
                                                    : `0 0 0 4px ${theme.palette.primary.main},
                                                       0 0 24px ${theme.palette.primary.main}50,
                                                       0 10px 28px ${theme.palette.primary.main}35`,
                                        },
                                    }}
                                />

                                {/* Online status */}
                                <Box
                                    aria-label={
                                        profile.status === "active"
                                            ? "Active"
                                            : "Inactive"
                                    }
                                    sx={{
                                        position: "absolute",
                                        right: 6,
                                        bottom: 6,
                                        width: 28,
                                        height: 28,
                                        border: "5px solid",
                                        borderColor: "background.paper",
                                        borderRadius: "50%",
                                        bgcolor:
                                            profile.status === "active"
                                                ? "#23a55a"
                                                : "#80848e",
                                    }}
                                />
                            </Box>
                        </Box>

                        {/* =================================================
                            NAME
                        ================================================== */}
                        <Box sx={{ mt: 2 }}>
                            <Stack
                                direction="row"
                                spacing={1}
                                sx={{
                                    alignItems: "center",
                                    flexWrap: "wrap",
                                }}
                            >
                                <Typography
                                    variant="h4"
                                    sx={{
                                        fontSize: {
                                            xs: "1.7rem",
                                            sm: "2rem",
                                        },
                                        fontWeight: 800,
                                    }}
                                >
                                    {profile.full_name || "Unnamed User"}
                                </Typography>

                                {profile.is_admin ? (
                                    <Chip
                                        label="ADMIN"
                                        size="small"
                                        color="primary"
                                        sx={{
                                            fontWeight: 700,
                                        }}
                                    />
                                ) : null}
                            </Stack>

                            <Typography variant="body1" color="text.secondary">
                                @{profile.username || "user"}
                            </Typography>

                            {profile.nickname ? (
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                    sx={{ mt: 0.5 }}
                                >
                                    {profile.nickname}
                                </Typography>
                            ) : null}
                        </Box>

                        <Divider sx={{ my: 3 }} />

                        {/* =================================================
                            INFORMATION
                        ================================================== */}
                        <Grid container spacing={3}>
                            {/* =================================================
                                ABOUT ME
                            ================================================== */}
                            <Grid size={{ xs: 12, md: 7 }}>
                                <Typography
                                    variant="overline"
                                    color="text.secondary"
                                    sx={{
                                        fontWeight: 700,
                                    }}
                                >
                                    About Me
                                </Typography>

                                <Box
                                    sx={{
                                        mt: 1,
                                        p: 2,
                                        borderRadius: 2,
                                        bgcolor: "action.hover",
                                    }}
                                >
                                    <Stack spacing={2}>
                                        <InfoRow
                                            icon={<WorkIcon />}
                                            label="Position"
                                            value={profile.position}
                                        />

                                        <InfoRow
                                            icon={<EmailIcon />}
                                            label="Email"
                                            value={profile.email}
                                        />

                                        <InfoRow
                                            icon={<PhoneIcon />}
                                            label="Contact Number"
                                            value={
                                                profile.contact_numbers?.length
                                                    ? profile.contact_numbers.join(
                                                          ", ",
                                                      )
                                                    : "—"
                                            }
                                        />
                                    </Stack>
                                </Box>
                            </Grid>

                            {/* =================================================
                                ACCOUNT INFORMATION
                            ================================================== */}
                            <Grid size={{ xs: 12, md: 5 }}>
                                <Typography
                                    variant="overline"
                                    color="text.secondary"
                                    sx={{
                                        fontWeight: 700,
                                    }}
                                >
                                    Account Information
                                </Typography>

                                <Box
                                    sx={{
                                        mt: 1,
                                        p: 2,
                                        borderRadius: 2,
                                        bgcolor: "action.hover",
                                    }}
                                >
                                    <Stack spacing={2}>
                                        {/* STATUS */}
                                        <Box>
                                            <Stack
                                                direction="row"
                                                spacing={1.5}
                                                sx={{
                                                    alignItems: "center",
                                                }}
                                            >
                                                <Box
                                                    sx={{
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent:
                                                            "center",
                                                        flexShrink: 0,
                                                        color: "text.secondary",
                                                        "& svg": {
                                                            fontSize: 21,
                                                        },
                                                    }}
                                                >
                                                    <BadgeIcon />
                                                </Box>

                                                <Box
                                                    sx={{
                                                        minWidth: 0,
                                                        flex: 1,
                                                    }}
                                                >
                                                    <Typography
                                                        variant="caption"
                                                        color="text.secondary"
                                                        display="block"
                                                        sx={{
                                                            fontWeight: 600,
                                                        }}
                                                    >
                                                        Status
                                                    </Typography>

                                                    <Box
                                                        sx={{
                                                            mt: 0.25,
                                                        }}
                                                    >
                                                        <Chip
                                                            label={
                                                                profile.status ||
                                                                "Unknown"
                                                            }
                                                            size="small"
                                                            color={
                                                                profile.status ===
                                                                "active"
                                                                    ? "success"
                                                                    : "default"
                                                            }
                                                            sx={{
                                                                textTransform:
                                                                    "capitalize",
                                                            }}
                                                        />
                                                    </Box>
                                                </Box>
                                            </Stack>
                                        </Box>

                                        {/* =================================================
                                            CHANGE PASSWORD
                                        ================================================== */}
                                        <Box
                                            sx={{
                                                pt: 1,
                                            }}
                                        >
                                            <Button
                                                fullWidth
                                                variant="contained"
                                                color="warning"
                                                startIcon={<LockIcon />}
                                                onClick={() => {
                                                    setChangeOpen(true);
                                                }}
                                                sx={{
                                                    justifyContent:
                                                        "flex-start",
                                                    textTransform: "none",
                                                    fontWeight: 700,
                                                    borderRadius: 2,
                                                    px: 2,
                                                    py: 1.1,

                                                    background: (theme) =>
                                                        theme.palette.gradients
                                                            .secondary,

                                                    color: "#fff",

                                                    boxShadow: (theme) =>
                                                        theme.palette.shadows
                                                            .secondaryButton,
                                                    transition:
                                                        "transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease",

                                                    "&:hover": {
                                                        background: (theme) =>
                                                            theme.palette
                                                                .gradients
                                                                .secondaryHover,

                                                        transform:
                                                            "translateY(-2px)",

                                                        boxShadow: (theme) =>
                                                            theme.palette
                                                                .shadows
                                                                .secondaryButtonHover,

                                                        "& .MuiButton-startIcon":
                                                            {
                                                                transform:
                                                                    "scale(1.15) rotate(-8deg)",
                                                            },
                                                    },

                                                    "&:active": {
                                                        transform:
                                                            "translateY(0)",
                                                        boxShadow: (theme) =>
                                                            theme.palette
                                                                .shadows
                                                                .secondaryButtonActive,
                                                    },

                                                    "& .MuiButton-startIcon": {
                                                        transition:
                                                            "transform 0.2s ease",
                                                    },
                                                }}
                                            >
                                                Change Password
                                            </Button>
                                            <ChangePassword
                                                flash={flash}
                                                errors={errors}
                                                profile={profile}
                                                changeOpen={changeOpen}
                                                setChangeOpen={setChangeOpen}
                                            />
                                        </Box>

                                        <Divider />

                                        <InfoRow
                                            icon={<CalendarIcon />}
                                            label="Last Login"
                                            value={profile.last_login_at}
                                        />

                                        <InfoRow
                                            icon={<CalendarIcon />}
                                            label="Created"
                                            value={profile.created_at}
                                        />

                                        <InfoRow
                                            icon={<CalendarIcon />}
                                            label="Last Updated"
                                            value={profile.updated_at}
                                        />
                                    </Stack>
                                </Box>
                            </Grid>
                        </Grid>
                    </Box>
                </Box>
            </Box>

            {/* =========================================================
                EDIT PROFILE DIALOG
            ========================================================== */}
            <EditProfile
                flash={flash}
                errors={errors}
                profile={profile}
                editOpen={editOpen}
                setEditOpen={setEditOpen}
            />
        </CBoxContent>
    );
};

export default ProfileContent;
