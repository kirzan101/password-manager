import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Button from "@mui/material/Button";

const CEmptyState = ({
    headline = "Whoops",
    title = "Nothing here",
    text = "",
    image,
    actionText,
    onAction,
}) => {
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
            }}
        >
            {image && (
                <Box
                    component="img"
                    src={image}
                    alt={title}
                    sx={{
                        width: 250,
                        height: 250,
                        mb: 2,
                        objectFit: "contain",
                    }}
                />
            )}

            <Typography variant="h3" fontWeight={600}>
                {headline}
            </Typography>

            <Typography variant="h6" sx={{ mt: 1 }}>
                {title}
            </Typography>

            {text && (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ mt: 1, maxWidth: 400 }}
                >
                    {text}
                </Typography>
            )}

            {actionText && onAction && (
                <Button variant="contained" sx={{ mt: 3 }} onClick={onAction}>
                    {actionText}
                </Button>
            )}
        </Box>
    );
};

export default CEmptyState;
