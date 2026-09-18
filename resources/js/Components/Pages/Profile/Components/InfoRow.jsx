import { Stack, Box, Typography } from "@mui/material";

const InfoRow = ({ icon, label, value }) => {
    const displayValue =
        value !== null && value !== undefined && String(value).trim() !== ""
            ? String(value)
            : "Not provided";

    return (
        <Stack
            direction="row"
            spacing={1.5}
            sx={{
                alignItems: "center",
                minWidth: 0,
            }}
        >
            <Box
                sx={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    flexShrink: 0,
                    color: "text.secondary",
                    "& svg": {
                        fontSize: 21,
                    },
                }}
            >
                {icon}
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
                    {label}
                </Typography>

                <Typography
                    component="span"
                    variant="body2"
                    sx={{
                        display: "block",
                        overflow: "hidden",
                        textOverflow: "ellipsis",
                        whiteSpace: "nowrap",
                        fontWeight: 600,
                    }}
                >
                    {displayValue}
                </Typography>
            </Box>
        </Stack>
    );
};

export default InfoRow;
