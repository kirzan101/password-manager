import { CModal, CButtonAdd, CButtonClose, CButtonSubmit } from "@/Components";
import { useState } from "react";

import FormUserGroup from "./Forms/FormUserGroup";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const AddUserGroup = ({
    flash,
    errors,
    can,
    userGroupTypes,
    sx,
    onSuccess,
}) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const [form, setForm] = useState({
        name: "",
        code: "",
        description: "",
    });

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({
            name: "",
            code: "",
            description: "",
        });
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post("/user-groups", form, {
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

    const canCreate = can.includes("create-user_groups");

    return (
        <>
            {canCreate && <CButtonAdd sx={sx} onClick={() => setOpen(true)} />}

            <CModal
                title="Add User Group"
                titleIcon="AddIcon"
                width={450}
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <FormUserGroup
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        userGroupTypes={userGroupTypes}
                    />

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={() => setOpen(false)} />
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

export default AddUserGroup;
