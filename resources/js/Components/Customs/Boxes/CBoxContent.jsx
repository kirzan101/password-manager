import Box from "@mui/material/Box";

const CBoxContent = ({ children, ...props }) => {
    return (
        <Box sx={{ mt: 2 }} {...props}>
            {children}
        </Box>
    );
};

export default CBoxContent;
