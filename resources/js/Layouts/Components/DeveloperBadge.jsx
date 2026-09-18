import LaptopTwoToneIcon from "@mui/icons-material/LaptopTwoTone";
import { Tooltip } from "@mui/material";
import { useTheme } from "@mui/material/styles";

const DeveloperBadge = ({ user }) => {
    const consideredDeveloper = [
        "software quality assurance",
        "software developer",
        "system analyst",
        "software engineer",
        "full stack developer",
        "backend developer",
        "frontend developer",
        "mobile developer",
        "devops engineer",
        "programmer",
        "system administrator",
    ];

    // Normalize position for comparison
    const position = user.position ? user.position.toLowerCase() : "";

    const isDeveloper = consideredDeveloper.includes(position);
    const isAdmin = user.isAdmin;

    if (!isDeveloper || isAdmin) {
        return null; // Don't show badge if not a developer or if they're an admin
    }

    const theme = useTheme();
    const isDark = theme.palette.mode === "dark";

    return (
        <Tooltip title="Developer" placement="right">
            <LaptopTwoToneIcon
                sx={{
                    fontSize: "1rem",
                    color: isDark ? "lightblue" : "primary.main",
                    ml: 0.5,
                    mb: 0.5,
                    verticalAlign: "middle",
                }}
            />
        </Tooltip>
    );
};

export default DeveloperBadge;
