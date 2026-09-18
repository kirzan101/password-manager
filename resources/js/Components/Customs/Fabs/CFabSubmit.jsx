import { Fab, CircularProgress, Tooltip } from "@mui/material";
import SaveIcon from "@mui/icons-material/Save";

const CFabSubmit = ({
    loading = false,
    disabled = false,
    size = 72,
    onClick,
    sx = {},
    ...props
}) => {
    return (
        <Tooltip title="Save changes" placement="top">
            <Fab
                color="primary"
                aria-label="save"
                onClick={onClick}
                disabled={disabled || loading}
                sx={{
                    width: size,
                    height: size,
                    ...sx,
                }}
                {...props}
            >
                {loading ? (
                    <CircularProgress size={28} color="inherit" />
                ) : (
                    <SaveIcon
                        sx={{
                            fontSize: size * 0.45,
                        }}
                    />
                )}
            </Fab>
        </Tooltip>
    );
};

export default CFabSubmit;
