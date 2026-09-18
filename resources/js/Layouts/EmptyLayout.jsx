import React from "react";
import { Box } from "@mui/material";

const EmptyLayout = ({ children }) => {
    return (
        <Box
            sx={{
                minHeight: "100vh",
                width: "100%",
            }}
        >
            {children}
        </Box>
    );
};

export default EmptyLayout;
