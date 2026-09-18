import { CardActions } from "@mui/material";

const CCardActions = ({ children, ...props }) => {
    return <CardActions {...props}>{children}</CardActions>;
};

export default CCardActions;
