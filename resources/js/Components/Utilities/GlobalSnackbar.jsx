import {
    forwardRef,
    useImperativeHandle,
    useState,
    useEffect,
    useRef,
} from "react";
import Snackbar from "@mui/material/Snackbar";
import Alert from "@mui/material/Alert";

const GlobalSnackbar = forwardRef((props, ref) => {
    const [open, setOpen] = useState(false);
    const [message, setMessage] = useState("");
    const [severity, setSeverity] = useState("success");

    const timerRef = useRef();

    useImperativeHandle(ref, () => ({
        show: (msg, type = "success") => {
            setMessage(msg);
            setSeverity(type);
            setOpen(true);
        },
        close: () => setOpen(false),
    }));

    useEffect(() => {
        if (open) {
            clearTimeout(timerRef.current);
            timerRef.current = setTimeout(() => setOpen(false), 3000);
        }
        return () => clearTimeout(timerRef.current);
    }, [open]);

    return (
        <Snackbar
            open={open}
            anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
        >
            <Alert
                severity={severity}
                variant="filled"
                onClose={() => setOpen(false)}
            >
                {message}
            </Alert>
        </Snackbar>
    );
});

export default GlobalSnackbar;
