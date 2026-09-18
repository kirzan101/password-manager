import IconButton from "@mui/material/IconButton";
import Tooltip from "@mui/material/Tooltip";
import { iconMap } from "@/Utilities/icons";

const CIconButton = ({
    size = "medium",
    icon,
    tooltip,
    children,
    sx,
    ...props
}) => {
    let IconComponent = iconMap[icon];

    if (!IconComponent) {
        console.warn(
            `Icon "${icon}" does not exist in the iconMap. ` +
                `Using QuestionMarkIcon instead.`,
        );

        IconComponent = iconMap.QuestionMarkIcon;
    }

    const button = (
        <IconButton
            size={size}
            aria-label={props["aria-label"] ?? tooltip}
            sx={{
                m: 1,
                borderRadius: 2,
                transition: "all 0.2s ease",

                "& .MuiSvgIcon-root": {
                    transition: "transform 0.2s ease",
                },

                "&:hover": {
                    transform: "translateY(-2px)",
                    backgroundColor: "action.hover",
                    boxShadow: (theme) =>
                        `0 5px 14px ${theme.palette.primary.main}20`,
                },

                "&:hover .MuiSvgIcon-root": {
                    transform: "scale(1.12) rotate(-5deg)",
                },

                "&:active": {
                    transform: "translateY(0)",
                    boxShadow: "none",
                },

                "&:focus-visible": {
                    outline: "2px solid",
                    outlineColor: "primary.main",
                    outlineOffset: 2,
                },

                ...sx,
            }}
            {...props}
        >
            <IconComponent fontSize="inherit" />
            {children}
        </IconButton>
    );

    return tooltip ? (
        <Tooltip title={tooltip} arrow>
            <span>{button}</span>
        </Tooltip>
    ) : (
        button
    );
};

export default CIconButton;
