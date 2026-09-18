import Alert from "@mui/material/Alert";
import { useState } from "react";

const CAlertError = ({ children, ...props }) => {
    const [open, setOpen] = useState(true);

    if (!open) return null;

    return (
        <Alert
            variant="filled"
            severity="error"
            color="error"
            onClose={() => setOpen(false)}
            {...props}
        >
            {children}
        </Alert>
    );
};

export default CAlertError;
