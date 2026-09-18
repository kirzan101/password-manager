import { useEffect, useState } from "react";
import { CModal, CButtonEdit, CButtonClose, CButtonSubmit } from "@/Components";

import EditLabel from "@/Components/Utilities/EditLabel";
import FormUserGroup from "./Forms/FormUserGroup";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const EditUserGroup = ({
    userGroup,
    flash,
    errors,
    can,
    sx,
    userGroupTypes,
    onSuccess,
}) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const getFormData = (userGroup) => ({
        id: userGroup?.id ?? null,
        name: userGroup?.name ?? "",
        code: userGroup?.code ?? "",
        description: userGroup?.description ?? "",
    });

    const [form, setForm] = useState(getFormData(userGroup));

    // Store the original form value for logging purposes
    const [oldForm, setOldForm] = useState(null);

    // update form value when userGroup props change
    useEffect(() => {
        setForm(getFormData(userGroup));
    }, [userGroup]);

    // Open modal
    const handleOpen = () => {
        const initialForm = getFormData(userGroup);

        setForm(initialForm);
        setOldForm({ ...initialForm });
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
        setForm(getFormData(null));
        setOldForm(null);
    };

    // check if user has permission to update user group
    const canUpdate = can.includes("update-user_groups");

    const handleSubmit = (event) => {
        event.preventDefault();

        if (!canUpdate) return; // user does not have permission to update user group

        // submission here
        router.post(
            `/user-groups/${userGroup.id}`,
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

    const title = `${canUpdate ? "Editing" : "Viewing"} ${userGroup.name}`;
    const icon = canUpdate ? "EditIcon" : "PreviewIcon";

    return (
        <>
            {canUpdate ? (
                <CButtonEdit sx={sx} onClick={handleOpen}>
                    {userGroup.name}
                </CButtonEdit>
            ) : (
                <EditLabel label={userGroup.name} onClick={handleOpen} />
            )}

            <CModal
                title={title}
                titleIcon={icon}
                width={450}
                open={open}
                onClose={handleClose}
            >
                <form onSubmit={handleSubmit}>
                    <FormUserGroup
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        userGroupTypes={userGroupTypes}
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

export default EditUserGroup;
