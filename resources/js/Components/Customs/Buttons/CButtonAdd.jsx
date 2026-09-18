import Button from "@mui/material/Button";
import AddIcon from "@mui/icons-material/Add";

const CButtonAdd = ({
    size = "medium",
    variant = "contained",
    startIcon = <AddIcon />,
    children = "Add",
    sx,
    ...props
}) => {
    return (
        <Button
            size={size}
            variant={variant}
            startIcon={startIcon}
            sx={{
                m: 1,
                background: (theme) => theme.palette.gradients.primary,
                boxShadow: (theme) => theme.palette.shadows.primaryButton,
                transition: "all 0.2s ease",

                "&:hover": {
                    background: (theme) => theme.palette.gradients.primaryHover,
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
                ...sx,
            }}
            {...props}
        >
            {children}
        </Button>
    );
};

export default CButtonAdd;
