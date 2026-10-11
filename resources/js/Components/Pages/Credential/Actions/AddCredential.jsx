import { CModal, CButtonAdd, CButtonClose, CButtonSubmit } from "@/Components";
import { useState } from "react";

import FormCredential from "./Forms/FormCredential";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const AddCredential = ({ flash, errors, can, sx, onSuccess }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const [form, setForm] = useState({
        name: "",
        username: "",
        secret: "",
        notes: "",
    });

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({
            name: "",
            username: "",
            secret: "",
            notes: "",
        });
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post("/credentials", form, {
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

    const canCreate = can.includes("create-credentials");

    return (
        <>
            {canCreate && <CButtonAdd sx={sx} onClick={() => setOpen(true)} />}

            <CModal
                title="Add Credential"
                titleIcon="AddIcon"
                width={450}
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <FormCredential
                        form={form}
                        setForm={setForm}
                        errors={errors}
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

export default AddCredential;
