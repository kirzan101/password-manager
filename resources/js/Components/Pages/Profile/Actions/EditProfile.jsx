import { useState, useEffect } from "react";
import { CModal, CButtonClose, CButtonSubmit } from "@/Components";
import { Alert } from "@mui/material";
import { router } from "@inertiajs/react";

import FormBasicProfile from "./Forms/FormBasicProfile";
import { Box } from "@mui/material";

const EditProfile = ({
    flash,
    errors = {},
    profile = {},
    editOpen,
    setEditOpen,
    onSuccess,
}) => {
    const [btnDisabled, setBtnDisabled] = useState(false);

    const getFormData = (profile) => ({
        id: profile?.id ?? null,
        user_id: profile?.user_id ?? null,
        nickname: profile?.nickname ?? "",
        position: profile?.position ?? "",
        contact_numbers: profile?.contact_numbers ?? [],
        email: profile?.email ?? "",
    });

    const [form, setForm] = useState(getFormData(profile));

    // Store the original form value for logging purposes
    const [oldForm, setOldForm] = useState(null);

    // update form value when userGroup props change
    useEffect(() => {
        setForm(getFormData(profile));
    }, [profile]);

    // Open modal
    const handleOpen = () => {
        const initialForm = getFormData(profile);

        setForm(initialForm);
        setOldForm({ ...initialForm });
        setEditOpen(true);
    };

    // Automatically open the modal when editOpen changes to true
    useEffect(() => {
        if (editOpen) {
            handleOpen();
        }
    }, [editOpen]);

    const handleClose = () => {
        setEditOpen(false);
        setForm(getFormData(null));
        setOldForm(null);
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post(
            `/update-basic-profile`,
            {
                _method: "PUT",
                forceFormData: true,
                ...form,
                old_properties: oldForm, // add old_properties to the form data for logging purposes
            },
            {
                onSuccess: ({ props }) => {
                    handleClose();

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
            },
        );
    };

    return (
        <>
            <CModal
                title="Edit Profile"
                titleIcon="EditIcon"
                width={650}
                open={editOpen}
                onClose={handleClose}
            >
                <Alert
                    variant="filled"
                    severity="info"
                    color="secondary"
                    icon={false}
                    sx={{ mb: 2 }}
                >
                    🔒 Some profile fields are locked. Contact your
                    administrator to update them.
                </Alert>
                <form onSubmit={handleSubmit}>
                    <FormBasicProfile
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        profile={profile}
                    />

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={handleClose} />
                        <CButtonSubmit
                            sx={{ ml: 1, mr: 0 }}
                            loading={btnDisabled}
                        />
                    </Box>
                </form>
            </CModal>
        </>
    );
};

export default EditProfile;
