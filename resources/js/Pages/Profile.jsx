import { CBox } from "@/Components";
import { Head, usePage } from "@inertiajs/react";
import { Typography, Breadcrumbs } from "@mui/material";
import DefaultLayout from "@/Layouts/DefaultLayout";

import ProfileContent from "@/Components/Pages/Profile/ProfileContent";

const Profile = ({ flash, errors, profile }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Profile — ${appName}`} />
            <CBox>
                {/* <Breadcrumbs aria-label="breadcrumb">
                    <Typography
                        color="inherit"
                        href="/dashboard"
                        onClick={() => router.visit("/dashboard")}
                        sx={{ cursor: "pointer" }}
                    >
                        Dashboard
                    </Typography>
                    <Typography sx={{ color: "text.primary" }}>
                        Profile
                    </Typography>
                </Breadcrumbs> */}

                <ProfileContent
                    flash={flash}
                    errors={errors}
                    profile={profile}
                />
            </CBox>
        </>
    );
};

Profile.layout = (page) => <DefaultLayout>{page}</DefaultLayout>;

export default Profile;
