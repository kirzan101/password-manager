import { CModal, CButtonAdd, CButtonClose, CButtonSubmit } from "@/Components";
import { useState } from "react";

import FormUser from "./Forms/FormUser";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const AddUser = ({
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

    const initialForm = {
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
    };

    const [form, setForm] = useState(initialForm);

    const handleResetForm = () => {
        setForm({ ...initialForm }); // safer, if ever mutate nested object in form, it won't affect the initialForm value
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post("/users", form, {
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

    // check if user has permission to add user
    const canCreate = can.includes("create-users");

    return (
        <>
            {canCreate && <CButtonAdd sx={sx} onClick={() => setOpen(true)} />}

            <CModal
                title="Add User"
                titleIcon="AddIcon"
                width={750}
                open={open}
                onClose={() => setOpen(false)}
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
                    />

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                            mx: 0,
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

export default AddUser;
