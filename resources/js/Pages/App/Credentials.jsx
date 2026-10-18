import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import CredentialContent from "@/Components/Pages/Credential/CredentialContent";

import { Head, usePage, router } from "@inertiajs/react";

const Credentials = ({ flash, errors, can, categories }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Credentials — ${appName}`} />
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
                        Credentials
                    </Typography>
                </Breadcrumbs>

                <CredentialContent
                    flash={flash}
                    errors={errors}
                    can={can}
                    categories={categories}
                />
            </CBox>
        </>
    );
};

export default Credentials;
