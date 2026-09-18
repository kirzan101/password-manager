import { CModal, CButtonEdit, CButtonClose, CButtonSubmit } from "@/Components";
import { useEffect, useState } from "react";

import EditLabel from "@/Components/Utilities/EditLabel";
import FormUser from "./Forms/FormUser";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const EditUser = ({
    user,
    flash,
    errors,
    userGroups,
    accountTypes,
    roles,
    can,
    sx,
    onSuccess,
}) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const buildEmptyForm = () => ({
        username: "",
        email: "",
        first_name: "",
        middle_name: "",
        last_name: "",
        nickname: "",
        type: "",
        position: "",
        contact_numbers: [],
        user_group_id: "",
        role_ids: [],
        user_id: "",
        id: "",
    });

    const buildFormFromUser = (user) => ({
        id: user?.id || "",
        username: user?.username || "",
        email: user?.email || "",
        first_name: user?.first_name || "",
        middle_name: user?.middle_name || "",
        last_name: user?.last_name || "",
        nickname: user?.nickname || "",
        type: user?.type || "",
        position: user?.position || "",
        contact_numbers: user?.contact_numbers || [],
        user_group_id: user?.user_group_id || "",
        role_ids: user?.role_ids || [],
        user_id: user?.user_id || "",
    });

    const [form, setForm] = useState(buildEmptyForm);

    // Store the original form value for logging purposes
    const [oldForm, setOldForm] = useState(null);

    useEffect(() => {
        if (!user) return;
        setForm(buildFormFromUser(user));
    }, [user]);

    const handleOpen = () => {
        if (!user) return;

        const initialForm = buildFormFromUser(user);

        setForm(initialForm);
        setOldForm({ ...initialForm });
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
        setForm(buildEmptyForm());
        setOldForm(null);
    };

    // check if user has permission to update user
    const canUpdate = can.includes("update-users");

    const handleSubmit = (event) => {
        event.preventDefault();

        if (!canUpdate) return; // user does not have permission to update user

        // submission here
        router.post(
            `/users/${user.id}`,
            {
                _method: "PUT",
                forceFormData: true,
                ...form,
                profile_id: user.id, // user here is profile, not user model
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

    const title = `${canUpdate ? "Editing" : "Viewing"} ${user.name}`;
    const icon = canUpdate ? "EditIcon" : "PreviewIcon";

    return (
        <>
            {canUpdate ? (
                <CButtonEdit sx={sx} onClick={handleOpen}>
                    {user.name}
                </CButtonEdit>
            ) : (
                <EditLabel label={user.name} onClick={handleOpen} />
            )}

            <CModal
                title={title}
                titleIcon={icon}
                width={750}
                open={open}
                onClose={handleClose}
            >
                <form onSubmit={handleSubmit}>
                    <FormUser
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        userGroups={userGroups}
                        accountTypes={accountTypes}
                        roles={roles}
                        can={can}
                        isReadonly={!canUpdate}
                    />

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={handleClose} />
                        {canUpdate && (
                            <CButtonSubmit
                                sx={{ ml: 1, mr: 0 }}
                                loading={btnDisabled}
                            />
                        )}
                    </Box>
                </form>
            </CModal>
        </>
    );
};

export default EditUser;
