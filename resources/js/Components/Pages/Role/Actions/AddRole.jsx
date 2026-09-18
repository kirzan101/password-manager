import { useState } from "react";
import {
    CModalFull,
    CButtonAdd,
    CCard,
    CCardContent,
    CFabSubmit,
} from "@/Components";
import { router } from "@inertiajs/react";
import { Box, Grid, Typography } from "@mui/material";

import FormRole from "./Forms/FormRole";
import SelectRolePermissions from "../Fields/SelectRolePermissions";

const AddRole = ({
    flash,
    errors,
    sx,
    permissions,
    moduleLists,
    can,
    onSuccess,
}) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const [form, setForm] = useState({
        name: "",
        is_active: true,
        description: "",
        user_group_id: "",
        permissionIds: [],
    });

    const handlePermissionsChange = (permissions) => {
        setForm((prev) => ({
            ...prev,
            permissionIds: permissions,
        }));
    };

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({
            name: "",
            is_active: true,
            description: "",
            user_group_id: "",
            permissionIds: [],
        });
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post("/roles", form, {
            forceFormData: true,
            onSuccess: ({ props }) => {
                setOpen(false);

                // reset form value
                handleResetForm();

                // call onSuccess callback if provided
                onSuccess?.();
            },
            onError: () => {
                // emits("notification", "Some fields has an error.", "error");
                // add snackbar here
            },
            onBefore: () => {
                setBtnDisabled(true);
            },
            onFinish: () => {
                setBtnDisabled(false);
            },
        });
    };

    // check if user has permission to create roles
    const canCreateRole = can.includes("create-roles");

    return (
        <>
            {canCreateRole && (
                <CButtonAdd
                    sx={{
                        display: "inline-flex",
                        width: "auto", // 👈 critical
                        ...sx,
                    }}
                    onClick={() => setOpen(true)}
                />
            )}

            <CModalFull
                title="Add Role"
                titleIcon="AddIcon"
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <Grid container spacing={4}>
                        {/* Left Side */}
                        <Grid
                            size={{ xs: 12, md: 4 }}
                            sx={{
                                borderRight: {
                                    md: 1,
                                },
                                borderColor: "divider",
                                pr: {
                                    md: 3,
                                },
                            }}
                        >
                            <Typography variant="h6" sx={{ mb: 2 }}>
                                Role Details
                            </Typography>
                            <CCard>
                                <CCardContent>
                                    <Typography
                                        gutterBottom
                                        sx={{
                                            color: "text.secondary",
                                            fontSize: 14,
                                        }}
                                    >
                                        Fill in the form to add a new role.
                                    </Typography>
                                    <FormRole
                                        form={form}
                                        setForm={setForm}
                                        errors={errors}
                                        permissions={permissions}
                                        moduleLists={moduleLists}
                                    />
                                </CCardContent>
                            </CCard>
                        </Grid>

                        {/* Right Side */}
                        <Grid
                            size={{ xs: 12, md: 8 }}
                            sx={{
                                pl: {
                                    md: 2,
                                },
                            }}
                        >
                            <SelectRolePermissions
                                permissions={permissions}
                                moduleLists={moduleLists}
                                selectedPermissions={form.permissionIds}
                                onChange={handlePermissionsChange}
                                errors={errors}
                            />
                        </Grid>
                    </Grid>

                    <Box
                        sx={{
                            position: "absolute",
                            bottom: 48,
                            right: 24,
                        }}
                    >
                        <CFabSubmit
                            loading={btnDisabled}
                            onClick={handleSubmit}
                        />
                    </Box>
                </form>
            </CModalFull>
        </>
    );
};

export default AddRole;
