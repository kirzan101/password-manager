import { Avatar, Box } from "@mui/material";

const UserAvatar = ({ avatarUrl, initials, size = 40 }) => {
    return (
        <Box
            sx={{
                width: size,
                height: size,
                flexShrink: 0,
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
            }}
        >
            <Avatar
                src={avatarUrl ?? undefined}
                sx={(theme) => ({
                    width: size,
                    height: size,
                    flexShrink: 0,
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    bgcolor: "primary.main",
                    color: theme.palette.getContrastText(
                        theme.palette.primary.main,
                    ),

                    "& .MuiAvatar-img": {
                        width: "100%",
                        height: "100%",
                        objectFit: "cover",
                    },

                    ...(!avatarUrl && {
                        px: 0.5,
                        fontSize: size * 0.4,
                        fontWeight: 600,
                    }),
                })}
            >
                {!avatarUrl && initials
                    ? initials.slice(0, 2).toUpperCase()
                    : null}
            </Avatar>
        </Box>
    );
};

export default UserAvatar;
