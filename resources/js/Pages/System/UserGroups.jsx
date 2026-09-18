import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import UserGroupContent from "@/Components/Pages/UserGroup/UserGroupContent";

import { Head, usePage, router } from "@inertiajs/react";

const UserGroups = ({ flash, errors, can, user_group_types }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`User Group — ${appName}`} />
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
                        User Groups
                    </Typography>
                </Breadcrumbs>

                <UserGroupContent
                    flash={flash}
                    errors={errors}
                    can={can}
                    userGroupTypes={user_group_types}
                />
            </CBox>
        </>
    );
};

export default UserGroups;
