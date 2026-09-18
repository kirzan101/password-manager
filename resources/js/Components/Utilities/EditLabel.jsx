import { Typography } from "@mui/material";

const EditLabel = ({ label, onClick, sx }) => {
    return (
        <Typography
            variant="button"
            sx={{
                ...sx,
            }}
            onClick={onClick}
        >
            {label}
        </Typography>
    );
};

export default EditLabel;