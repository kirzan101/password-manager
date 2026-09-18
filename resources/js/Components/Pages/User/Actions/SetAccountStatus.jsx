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

const SetAccountStatus = ({ user, flash, errors, can, onSuccess, sx }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const toggleModal = () => {
        setOpen(!open);
    };

    const oppositeStatus = user.status === "active" ? "DEACTIVATE" : "ACTIVATE";
    const iconStatus =
        user.status === "active" ? "PersonRemoveIcon" : "PersonAddAlt1Icon";
    const colorStatus = user.status === "active" ? "error" : "accent";
    const isAdmin = user.is_admin;

    const handleSubmit = (event) => {
        event.preventDefault();

        // prevent changing status of admin user
        if (isAdmin) return;

        // submission here
        router.post(
            `/set-user-status/${user.id}`,
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
    const canSetAccountStatus = can.includes("set-status-users");

    return (
        <>
            <CIconButton
                icon={iconStatus}
                color={colorStatus}
                tooltip="Set Account Status"
                sx={sx}
                onClick={toggleModal}
                disabled={!canSetAccountStatus || isAdmin}
            />

            <CModal
                title="Set Account Status"
                titleIcon={iconStatus}
                width={450}
                open={open}
                onClose={toggleModal}
            >
                <form onSubmit={handleSubmit}>
                    <Typography gutterBottom>
                        Are you sure you want to <b>{oppositeStatus}</b> the
                        account status of <b>{user.name}</b>?
                    </Typography>

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={toggleModal} />
                        <CButtonSubmit
                            startIcon={iconStatus}
                            color={colorStatus}
                            children={`${oppositeStatus}`}
                            sx={{ ml: 1, mr: 0 }}
                            loading={btnDisabled}
                        />
                    </Box>
                </form>
            </CModal>
        </>
    );
};

export default SetAccountStatus;
