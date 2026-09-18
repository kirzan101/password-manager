import { Card } from "@mui/material";

const CCard = ({ title, children, sx, ...props }) => {
    return (
        <Card
            sx={[
                {
                    borderRadius: 4,
                    boxShadow: 3,
                    p: 1,
                },
                sx,
            ]}
            {...props}
        >
            {children}
        </Card>
    );
};

export default CCard;
