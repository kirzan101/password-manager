import { CBox } from "@/Components";
import { Typography, Breadcrumbs, Link } from "@mui/material";
import { Head, usePage, router } from "@inertiajs/react";

import ModuleContent from "@/Components/Pages/Module/ModuleContent";

const Modules = ({ flash, errors, can, categories }) => {
    const page = usePage();
    const appName = page.props.appName || "Laravel React App";

    return (
        <>
            <Head title={`Modules — ${appName}`} />
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
                        Modules
                    </Typography>
                </Breadcrumbs>

                <ModuleContent flash={flash} errors={errors} can={can} categories={categories} />
            </CBox>
        </>
    );
};

export default Modules;
