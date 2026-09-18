import Alert from "@mui/material/Alert";
import { useState } from "react";

const CAlertSuccess = ({ children, ...props }) => {
    const [open, setOpen] = useState(true);

    if (!open) return null;

    return (
        <Alert
            variant="filled"
            severity="success"
            color="success"
            onClose={() => setOpen(false)}
            {...props}
        >
            {children}
        </Alert>
    );
};

export default CAlertSuccess;
