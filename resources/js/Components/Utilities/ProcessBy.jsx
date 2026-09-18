import { useEffect, useRef, useState } from "react";
import { Box, Tooltip, Typography } from "@mui/material";
import UserAvatar from "@/Components/Utilities/UserAvatar";

const ProcessBy = ({ avatarUrl, initials, name, fontSize = 14 }) => {
    const textRef = useRef(null);
    const [isTruncated, setIsTruncated] = useState(false);

    const avatarSize = Math.round(fontSize * 2);

    useEffect(() => {
        const checkTruncation = () => {
            const element = textRef.current;

            if (element) {
                setIsTruncated(element.scrollWidth > element.clientWidth);
            }
        };

        checkTruncation();

        const observer = new ResizeObserver(checkTruncation);

        if (textRef.current) {
            observer.observe(textRef.current);
        }

        return () => observer.disconnect();
    }, [name]);

    return (
        <Box
            sx={{
                display: "flex",
                alignItems: "center",
                gap: 1,
                minWidth: 0,
                width: "100%",
            }}
        >
            <UserAvatar
                avatarUrl={avatarUrl}
                initials={initials}
                size={avatarSize}
            />

            <Box sx={{ flex: 1, minWidth: 0 }}>
                <Tooltip title={isTruncated ? name : ""} placement="top" arrow>
                    <Typography
                        ref={textRef}
                        component="span"
                        sx={{
                            display: "block",
                            fontSize: `${fontSize}px`,
                            lineHeight: 1.2,
                            overflow: "hidden",
                            textOverflow: "ellipsis",
                            whiteSpace: "nowrap",
                        }}
                    >
                        {name}
                    </Typography>
                </Tooltip>
            </Box>
        </Box>
    );
};

export default ProcessBy;
