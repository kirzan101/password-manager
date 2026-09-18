import { Box, Typography } from "@mui/material";

const EmptyResult = ({ message = "No results found." }) => {
    return (
        <Box
            sx={{
                minHeight: "60vh",
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
                justifyContent: "center",
                textAlign: "center",
                px: 2,
                gap: 2,
            }}
        >
            <Box
                component="img"
                src="/images/empty.svg"
                alt="No Results"
                sx={{
                    width: 180,
                    maxWidth: "100%",
                }}
            />

            <Typography variant="body1" color="text.secondary" fontWeight={500}>
                {message}
            </Typography>
        </Box>
    );
};

export default EmptyResult;
