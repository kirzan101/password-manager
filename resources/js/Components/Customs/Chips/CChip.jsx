import Chip from "@mui/material/Chip";

const CChip = ({ label, color = "default", size = "medium", sx, ...props }) => {
    return <Chip label={label} color={color} size={size} sx={sx} {...props} />;
};

export default CChip;
