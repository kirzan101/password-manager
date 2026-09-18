import { useState } from "react";
import { TextField, InputAdornment, IconButton } from "@mui/material";
import { Visibility, VisibilityOff } from "@mui/icons-material";

const CPasswordField = ({ id = "password", children, slotProps, ...props }) => {
    const [showPassword, setShowPassword] = useState(false);

    return (
        <TextField
            id={id}
            fullWidth
            size="small"
            variant="outlined"
            color="textField"
            type={showPassword ? "text" : "password"}
            placeholder="Enter your password..."
            {...props}
            slotProps={{
                ...slotProps,
                input: {
                    ...slotProps?.input,
                    endAdornment: (
                        <>
                            {slotProps?.input?.endAdornment}

                            <InputAdornment position="end">
                                <IconButton
                                    onClick={() =>
                                        setShowPassword((prev) => !prev)
                                    }
                                    edge="end"
                                    tabIndex={-1}
                                >
                                    {showPassword ? (
                                        <VisibilityOff />
                                    ) : (
                                        <Visibility />
                                    )}
                                </IconButton>
                            </InputAdornment>
                        </>
                    ),
                },
            }}
        >
            {children}
        </TextField>
    );
};

export default CPasswordField;
