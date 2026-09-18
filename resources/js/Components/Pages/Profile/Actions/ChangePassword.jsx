import { useState, useEffect } from "react";
import { CModal, CButtonClose, CButtonSubmit } from "@/Components";
import FormChangePassword from "./Forms/FormChangePassword";
import { Alert, Box } from "@mui/material";
import { router } from "@inertiajs/react";

const ChangePassword = ({
    flash,
    errors = {},
    profile = {},
    changeOpen,
    setChangeOpen,
    onSuccess,
}) => {
    const [btnDisabled, setBtnDisabled] = useState(false);

    const getFormData = (profile) => ({
        current_password: "",
        new_password: "",
        confirm_password: "",
        profile_id: profile?.id ?? null,
    });

    const [form, setForm] = useState(getFormData(profile));

    // Open modal
    const handleOpen = () => {
        const initialForm = getFormData(profile);

        setForm(initialForm);
        setChangeOpen(true);
    };

    // Automatically open the modal when changeOpen changes to true
    useEffect(() => {
        if (changeOpen) {
            handleOpen();
        }
    }, [changeOpen]);

    const handleClose = () => {
        setChangeOpen(false);
        setForm(getFormData(null));
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post(
            `/change-password`,
            {
                _method: "PUT",
                forceFormData: true,
                ...form,
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
                title="Change Password"
                titleIcon="LockIcon"
                width={500}
                open={changeOpen}
                onClose={handleClose}
            >
                <Alert
                    variant="filled"
                    severity="info"
                    color="secondary"
                    sx={{ mb: 2 }}
                >
                    Enter your current password and choose a new one.
                </Alert>
                <form onSubmit={handleSubmit}>
                    <FormChangePassword
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

export default ChangePassword;
