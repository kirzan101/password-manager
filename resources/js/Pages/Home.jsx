import { CBox } from "@/Components";
import { Typography } from "@mui/material";

const Home = ({ flash, errors }) => {
    return (
        <CBox
            sx={{
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
                justifyContent: "center",
                height: "80vh",
            }}
        >
            <Typography variant="h3" gutterBottom>
                Welcome to the Home Page!
            </Typography>
            <Typography variant="h6">
                This is a simple Laravel React application using Inertia.js and
                Material-UI.
            </Typography>
        </CBox>
    );
};

export default Home;
