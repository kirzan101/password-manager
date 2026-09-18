import Alert from "@mui/material/Alert";

const CAlert = ({ severity, children, ...props }) => {
    return (
        <Alert variant="filled" severity={severity} {...props}>
            {children}
        </Alert>
    );
};

export default CAlert;
