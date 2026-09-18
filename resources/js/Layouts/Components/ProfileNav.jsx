import { Paper, Typography, Box, Tooltip } from "@mui/material";
import AdminBadge from "./AdminBadge";
import DeveloperBadge from "./DeveloperBadge";
import { AvatarUpload } from "@/Components";
import { router } from "@inertiajs/react";

const getRoleAvatarSx = (isAdmin) => ({
    bgcolor: "primary.main",
    fontSize: 28,
    fontWeight: 700,

    // Separation from the surrounding content
    border: "3px solid",
    borderColor: "background.paper",

    // Role indicator
    outline: "2px solid",
    outlineColor: isAdmin ? "secondary.main" : "primary.main",
    outlineOffset: 2,

    boxShadow: (theme) =>
        isAdmin
            ? `0 0 0 4px ${theme.palette.secondary.main}20,
               0 0 16px ${theme.palette.secondary.main}40`
            : `0 0 0 4px ${theme.palette.primary.main}15,
               0 0 14px ${theme.palette.primary.main}25`,

    transition: "transform 0.2s ease, box-shadow 0.2s ease",

    "&:hover": {
        transform: "scale(1.04)",

        boxShadow: (theme) =>
            isAdmin
                ? `0 0 0 3px ${theme.palette.secondary.main},
                   0 0 22px ${theme.palette.secondary.main}55`
                : `0 0 0 3px ${theme.palette.primary.main},
                   0 0 20px ${theme.palette.primary.main}40`,
    },
});

const ProfileNav = ({ user }) => {
    const avatarUrl = user.avatar;
    const initials = user.initials || "NA";
    const position = user.position || "N/A";
    const name = user.name || "Guest User";
    const isAdmin = user.isAdmin || false;

    const handleAvatarChange = (blob) => {
        router.post(
            "/change-avatar",
            {
                _method: "PUT",
                avatar: blob,
            },
            {
                forceFormData: true,
                onError: (errors) => {
                    console.error("Error changing avatar", errors);
                },
            },
        );
    };

    return (
        <Paper
            variant="text"
            square
            sx={{
                overflow: "hidden",
                bgcolor: "background.paper",
            }}
        >
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    gap: 2,
                    p: 2,
                    minHeight: 100,
                }}
            >
                {/* AVATAR */}
                <AvatarUpload
                    avatarUrl={avatarUrl}
                    initials={initials}
                    size={56}
                    onChange={handleAvatarChange}
                    avatarSx={getRoleAvatarSx(isAdmin)}
                />

                {/* USER INFO */}
                <Box
                    sx={{
                        minWidth: 0,
                        flex: 1,
                    }}
                >
                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            flexWrap: "wrap",
                            columnGap: 0.75,
                            rowGap: 0.25,
                        }}
                    >
                        <Tooltip title="View your profile" arrow>
                            <Typography
                                component="div"
                                sx={{
                                    fontWeight: 700,
                                    lineHeight: 1.25,
                                    wordBreak: "break-word",
                                    cursor: "pointer",
                                }}
                                onClick={() => router.visit("/profile")}
                            >
                                {name}

                                {isAdmin && <AdminBadge />}

                                <DeveloperBadge user={user} />
                            </Typography>
                        </Tooltip>
                    </Box>

                    {position && (
                        <Typography
                            variant="body2"
                            color="text.secondary"
                            sx={{
                                mt: 0.25,
                                lineHeight: 1.3,
                            }}
                        >
                            {position}
                        </Typography>
                    )}
                </Box>
            </Box>
        </Paper>
    );
};

export default ProfileNav;
