import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import { Head, usePage, router } from "@inertiajs/react";

import SettingContent from "@/Components/Pages/Setting/SettingContent";

const Settings = ({
    flash,
    errors,
    can,
    userGroupTypes,
    permissions,
    moduleLists,
    auth,
    modules,
    categories,
    permissionsByModule,
}) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";
    const accessibleRoutes = auth.user.accessibleModules || [];
    const settingsModules = modules
        .filter((module) => module.category?.toLowerCase() === "settings")
        .sort((a, b) => (a.order || 0) - (b.order || 0)); // Sort by order property, default to 0 if not present

    return (
        <>
            <Head title={`Settings — ${appName}`} />
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
                        Settings
                    </Typography>
                </Breadcrumbs>

                <SettingContent
                    flash={flash}
                    errors={errors}
                    can={can}
                    userGroupTypes={userGroupTypes}
                    permissions={permissions}
                    moduleLists={moduleLists}
                    accessibleRoutes={accessibleRoutes}
                    settingsModules={settingsModules}
                    categories={categories}
                    permissionsByModule={permissionsByModule}
                />
            </CBox>
        </>
    );
};

export default Settings;
