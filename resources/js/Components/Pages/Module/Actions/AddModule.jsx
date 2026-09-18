import { CModal, CButtonAdd, CButtonClose, CButtonSubmit } from "@/Components";
import { useState } from "react";

import FormModule from "./Forms/FormModule";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const AddModule = ({ flash, errors, can, categories, sx, onSuccess }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const initialForm = {
        name: "",
        icon: "",
        route: "",
        category: "",
        order: 0,
        is_active: true,
    };

    const [form, setForm] = useState(initialForm);

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({ ...initialForm }); // safer, if ever mutate nested object in form, it won't affect the initialForm value
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post("/modules", form, {
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

    // check if user has permission to create module
    const canCreateModule = can.includes("create-modules");

    return (
        <>
            {canCreateModule && (
                <CButtonAdd sx={sx} onClick={() => setOpen(true)} />
            )}

            <CModal
                title="Add Module"
                width={450}
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <FormModule
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        categories={categories}
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

export default AddModule;
