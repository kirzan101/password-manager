import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import { Head, usePage, router } from "@inertiajs/react";

import RoleContent from "@/Components/Pages/Role/RoleContent";

const Roles = ({
    flash,
    errors,
    permissions,
    moduleLists,
    can,
}) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Roles — ${appName}`} />
            <CBox>
                <Breadcrumbs aria-label="breadcrumb">
                    {/* <Link underline="hover" color="inherit" href="/dashboard">
                        Dashboard
                    </Link> */}
                    <Typography
                        color="inherit"
                        href="/dashboard"
                        onClick={() => router.visit("/dashboard")}
                        sx={{ cursor: "pointer" }}
                    >
                        Dashboard
                    </Typography>
                    <Typography sx={{ color: "text.primary" }}>
                        Roles
                    </Typography>
                </Breadcrumbs>

                <RoleContent
                    flash={flash}
                    errors={errors}
                    permissions={permissions}
                    moduleLists={moduleLists}
                    can={can}
                />
            </CBox>
        </>
    );
};

export default Roles;
