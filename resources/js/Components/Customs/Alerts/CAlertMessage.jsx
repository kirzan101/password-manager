import Alert from "@mui/material/Alert";
import { useState } from "react";

const CAlertMessage = ({ children, ...props }) => {
    const [open, setOpen] = useState(true);

    if (!open) return null;

    return (
        <Alert
            variant="filled"
            severity="info"
            color="primary"
            onClose={() => setOpen(false)}
            {...props}
        >
            {children}
        </Alert>
    );
};

export default CAlertMessage;
