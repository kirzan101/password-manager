import { isValidElement } from "react";
import TextField from "@mui/material/TextField";

const normalizeHelperText = (helperText) => {
    // Don't pass booleans to MUI
    if (typeof helperText === "boolean") {
        return undefined;
    }

    // Allow null/undefined
    if (helperText == null) {
        return undefined;
    }

    // Valid primitive values
    if (typeof helperText === "string" || typeof helperText === "number") {
        return helperText;
    }

    // React element
    if (isValidElement(helperText)) {
        return helperText;
    }

    // Common validation object: { message: "..." }
    if (
        typeof helperText === "object" &&
        typeof helperText.message === "string"
    ) {
        return helperText.message;
    }

    // Warn in development for unexpected values
    if (process.env.NODE_ENV !== "production") {
        console.warn("Invalid helperText:", helperText);
    }

    return undefined;
};

const CTextField = ({
    children,
    helperText,
    isReadonly = false,
    tabIndex,
    slotProps = {},
    type,
    ...props
}) => (
    <TextField
        {...props}
        type={type}
        fullWidth
        size="small"
        variant="outlined"
        color="textField"
        helperText={normalizeHelperText(helperText)}
        slotProps={{
            ...slotProps,

            input: {
                ...slotProps.input,
                readOnly: isReadonly,
            },

            inputLabel: {
                ...slotProps.inputLabel,
                ...(type === "date" && { shrink: true }),
            },

            htmlInput: {
                ...slotProps.htmlInput,
                tabIndex: isReadonly ? -1 : tabIndex,
            },
        }}
    >
        {children}
    </TextField>
);

export default CTextField;
