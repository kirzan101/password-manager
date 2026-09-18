import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import { Head, usePage, router } from "@inertiajs/react";

import PermissionContent from "@/Components/Pages/Permission/PermissionContent";

const Permissions = ({ flash, errors, can, permissionsByModule }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Permissions — ${appName}`} />
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
                        Permissions
                    </Typography>
                </Breadcrumbs>

                <PermissionContent
                    flash={flash}
                    errors={errors}
                    can={can}
                    permissionsByModule={permissionsByModule}
                />
            </CBox>
        </>
    );
};

export default Permissions;
