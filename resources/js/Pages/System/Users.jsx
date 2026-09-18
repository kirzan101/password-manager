import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import UserContent from "@/Components/Pages/User/UserContent";

import { Head, usePage, router } from "@inertiajs/react";

const Users = ({ flash, errors, user_groups, account_types, roles, can }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Users — ${appName}`} />
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
                        Users
                    </Typography>
                </Breadcrumbs>

                <UserContent
                    flash={flash}
                    errors={errors}
                    userGroups={user_groups}
                    accountTypes={account_types}
                    roles={roles}
                    can={can}
                />
            </CBox>
        </>
    );
};

export default Users;
