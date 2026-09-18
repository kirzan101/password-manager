import StarIcon from "@mui/icons-material/Star";
import { Tooltip } from "@mui/material";

const AdminBadge = () => {
    return (
        <Tooltip title="Admin" placement="right">
            <StarIcon
                sx={{
                    fontSize: "1rem",
                    color: "gold",
                    ml: 0.5,
                    mb: 0.5,
                    verticalAlign: "middle",
                }}
            />
        </Tooltip>
    );
};

export default AdminBadge;
