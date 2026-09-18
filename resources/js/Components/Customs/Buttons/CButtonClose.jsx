import Button from "@mui/material/Button";
import CloseIcon from "@mui/icons-material/Close";

const CButtonClose = ({
    size = "medium",
    variant = "text",
    color = "closeText",
    startIcon = <CloseIcon />,
    children = "Close",
    sx,
    ...props
}) => {
    return (
        <Button
            tabIndex={1}
            size={size}
            variant={variant}
            startIcon={startIcon}
            color={color}
            sx={{
                m: 1,
                px: 1.5,
                borderRadius: 2,
                fontWeight: 600,
                textTransform: "none",
                transition: "all 0.2s ease",

                "& .MuiButton-startIcon": {
                    transition: "transform 0.2s ease",
                },

                "&:hover": {
                    backgroundColor: "action.hover",
                    transform: "translateY(-1px)",
                },

                "&:hover .MuiButton-startIcon": {
                    transform: "scale(1.1) rotate(-5deg)",
                },

                "&:active": {
                    transform: "translateY(0)",
                },

                ...sx,
            }}
            {...props}
        >
            {children}
        </Button>
    );
};

export default CButtonClose;
