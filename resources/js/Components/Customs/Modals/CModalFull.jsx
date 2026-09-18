import { Modal, Box, Typography, Fade, Button } from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";
import { iconMap } from "../../../Utilities/icons";

const CModalFull = ({
    title,
    titleIcon,
    description,
    children,
    open,
    onClose,
    ...props
}) => {
    /*
    |--------------------------------------------------------------------------
    | Modal styles
    |--------------------------------------------------------------------------
    */
    const modalStyle = {
        top: 0,
        left: 0,

        width: "100vw",
        height: "100vh",

        bgcolor: "background.default",

        display: "flex",
        flexDirection: "column",

        overflow: "hidden",

        position: "relative", // FOR FAB POSITIONING
    };

    // use the titleIcon prop to get the corresponding icon component from the iconMap
    // uppercase the first letter of the titleIcon prop to match the key in the iconMap
    const TitleIcon = titleIcon ? iconMap[titleIcon] : null;

    return (
        <Modal
            open={open}
            onClose={onClose}
            aria-labelledby="cmodalfull-title"
            aria-describedby="cmodalfull-description"
            {...props}
        >
            <Fade in={open} timeout={300}>
                <Box sx={modalStyle}>
                    {/* Header */}
                    {(title || description) && (
                        <Box
                            sx={{
                                px: 4,
                                pt: 3,
                                pb: 2,
                                display: "flex",
                                justifyContent: "space-between",
                                alignItems: "flex-start",
                                gap: 2,
                            }}
                        >
                            {/* Left Side */}
                            <Box>
                                {(titleIcon || title) && (
                                    <Box
                                        sx={{
                                            display: "flex",
                                            alignItems: "center",
                                            gap: 1,
                                            mb: 2,
                                        }}
                                    >
                                        {titleIcon && <TitleIcon />}

                                        {title && (
                                            <Typography
                                                id="cmodalfull-title"
                                                variant="h6"
                                                component="h2"
                                            >
                                                {title}
                                            </Typography>
                                        )}
                                    </Box>
                                )}

                                {description && (
                                    <Typography
                                        id="cmodalfull-description"
                                        variant="body2"
                                        color="text.secondary"
                                        sx={{ mt: 1 }}
                                    >
                                        {description}
                                    </Typography>
                                )}
                            </Box>

                            {/* Right Side */}
                            <Box>
                                <Button
                                    variant="contained"
                                    color="error"
                                    startIcon={<CloseIcon />}
                                    onClick={onClose}
                                    sx={{
                                        background: (theme) =>
                                            theme.palette.gradients.error,
                                        boxShadow: (theme) =>
                                            theme.palette.shadows.errorButton,
                                        transition: "all 0.2s ease",

                                        "&:hover": {
                                            background: (theme) =>
                                                theme.palette.gradients
                                                    .errorHover,
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
                                            transform:
                                                "scale(1.15) rotate(-5deg)",
                                        },
                                    }}
                                >
                                    Close
                                </Button>
                            </Box>
                        </Box>
                    )}

                    {/* Content */}
                    <Box
                        sx={{
                            flex: 1,
                            overflowY: "auto",
                            px: 4,
                            py: 3,
                        }}
                    >
                        {children}
                    </Box>
                </Box>
            </Fade>
        </Modal>
    );
};

export default CModalFull;
