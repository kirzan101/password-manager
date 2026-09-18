import { useTheme } from "@mui/material/styles";

import CEmptyState from "@/Components/Customs/EmptyStates/CEmptyState";
import CButton from "@/Components/Customs/Buttons/CButton";
import { router } from "@inertiajs/react";

const Error = ({ code = 500, message = "Something went wrong" }) => {
    const titles = {
        403: "Access Restricted",
        404: "Page Not Found",
        500: "Something Went Wrong",
    };

    const theme = useTheme();
    const isDark = theme.palette.mode === "dark";

    const imageUrls = {
        403: isDark
            ? "/images/errors/error_403_dark.svg"
            : "/images/errors/error_403_light.svg",
        404: isDark
            ? "/images/errors/error_404_dark.svg"
            : "/images/errors/error_404_light.svg",
        500: isDark
            ? "/images/errors/error_500_dark.svg"
            : "/images/errors/error_500_light.svg",
    };

    return (
        <>
            <CEmptyState
                headline={`Whoops, ${code}`}
                title={titles[code] || "Error"}
                text={message}
                image={imageUrls[code] || "/images/errors/error_500_light.svg"}
            />
            <CButton
                sx={{ display: "block", mx: "auto" }}
                onClick={() =>
                    router.visit("/", {
                        replace: true,
                    })
                }
            >
                Back to Home
            </CButton>
        </>
    );
};

export default Error;
