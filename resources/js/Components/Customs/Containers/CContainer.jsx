import { Container } from "@mui/material";

const CContainer = ({ children, ...props }) => {
    return (
        <Container {...props}>
            {children}
        </Container>
    );
};

export default CContainer;
