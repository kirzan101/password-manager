import Box from "@mui/material/Box";

const CBox = ({ children, ...props }) => {
    return <Box {...props}>{children}</Box>;
};

export default CBox;
