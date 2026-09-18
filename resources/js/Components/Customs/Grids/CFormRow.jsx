import Grid from "@mui/material/Grid";

const CFormRow = ({ children, spacing = 2, ...props }) => {
    return (
        <Grid container spacing={spacing} {...props}>
            {children}
        </Grid>
    );
};

export default CFormRow;
