import Button from "@mui/material/Button";
import { iconMap } from "@/Utilities/icons";

const CButtonSubmit = ({
    size = "medium",
    variant = "contained",
    loadingPosition = "start",
    startIcon = "SaveIcon",
    children = "Save",
    sx,
    ...props
}) => {
    let IconComponent = iconMap[startIcon];

    if (!IconComponent) {
        console.warn(
            `Icon "${startIcon}" does not exist in the iconMap. ` +
                `Using SaveIcon instead.`,
        );

        IconComponent = iconMap.SaveIcon;
    }

    return (
        <Button
            type="submit"
            size={size}
            variant={variant}
            startIcon={IconComponent ? <IconComponent /> : null}
            loadingPosition={loadingPosition}
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

export default CButtonSubmit;
