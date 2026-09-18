import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import { Head, usePage, router } from "@inertiajs/react";

import ActivityLogContent from "@/Components/Pages/ActivityLog/ActivityLogContent";

const ActivityLogs = ({ flash, errors, can }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Activity Logs — ${appName}`} />
            <CBox>
                <Breadcrumbs aria-label="breadcrumb">
                    <Typography
                        color="inherit"
                        href="/dashboard"
                        onClick={() => router.visit("/dashboard")}
                        sx={{ cursor: "pointer" }}
                    >
                        Dashboard
                    </Typography>
                    <Typography sx={{ color: "text.primary" }}>
                        Activity Logs
                    </Typography>
                </Breadcrumbs>

                <ActivityLogContent flash={flash} errors={errors} can={can} />
            </CBox>
        </>
    );
};

export default ActivityLogs;
