import {
    CModal,
    CButton,
    CButtonEdit,
    CButtonClose,
    CButtonSubmit,
    CIconButton,
} from "@/Components";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import { useEffect, useState } from "react";
import { router } from "@inertiajs/react";

const ResetPassword = ({ user, flash, errors, can, onSuccess, sx }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const toggleModal = () => {
        setOpen(!open);
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post(
            `/reset-password/${user.id}`,
            {
                _method: "PUT",
                forceFormData: true,
            },
            {
                onSuccess: ({ props }) => {
                    toggleModal();

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

    // check if user has permission to update user
    const canResetPassword = can.includes("reset-users");

    return (
        <>
            <CIconButton
                icon="RotateLeftIcon"
                color="iconColor"
                tooltip="Reset Password"
                sx={sx}
                onClick={toggleModal}
                disabled={!canResetPassword}
            />

            <CModal
                title="Reset Password"
                titleIcon="RotateLeftIcon"
                width={450}
                open={open}
                onClose={toggleModal}
            >
                <form onSubmit={handleSubmit}>
                    <Typography gutterBottom>
                        Are you sure you want to reset the password of{" "}
                        <b>{user.name}</b>?
                    </Typography>

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose
                            onClick={toggleModal}
                            disabled={btnDisabled}
                        />
                        <CButtonSubmit
                            startIcon="RotateLeftIcon"
                            children="Reset"
                            sx={{ ml: 1, mr: 0 }}
                            loading={btnDisabled}
                        />
                    </Box>
                </form>
            </CModal>
        </>
    );
};

export default ResetPassword;
