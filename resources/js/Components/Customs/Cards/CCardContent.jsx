import { CardContent } from "@mui/material";

const CCardContent = ({ children, ...props }) => {
    return (
        <CardContent className="p-4" {...props}>
            {children}
        </CardContent>
    );
};

export default CCardContent;
