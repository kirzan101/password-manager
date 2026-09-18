import Button from "@mui/material/Button";
import EditIcon from "@mui/icons-material/Edit";

const CButtonEdit = ({
    size = "medium",
    variant = "text",
    endIcon = <EditIcon />,
    children = "Edit",
    sx,
    ...props
}) => {
    return (
        <Button
            size={size}
            variant={variant}
            endIcon={endIcon}
            sx={{
                m: 1,
                px: 1.5,
                borderRadius: 2,
                fontWeight: 700,
                textTransform: "none",
                color: (theme) => theme.palette.buttonTextColor.main,
                transition: "all 0.2s ease",

                "& .MuiButton-endIcon": {
                    transition: "transform 0.2s ease",
                },

                "&:hover": {
                    color: "#fff",
                    background: (theme) => theme.palette.gradients.primary,
                    transform: "translateY(-2px)",
                    boxShadow: (theme) => theme.palette.shadows.primaryButton,

                    "& .MuiButton-endIcon": {
                        transform: "rotate(-8deg) scale(1.1)",
                    },
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

export default CButtonEdit;
