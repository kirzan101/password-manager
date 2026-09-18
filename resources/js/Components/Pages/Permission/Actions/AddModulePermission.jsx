import { useEffect, useState } from "react";
import {
    CModal,
    CButtonAdd,
    CButtonClose,
    CButtonSubmit,
    CFormRow,
    CFormGrid,
    CTextField,
    CAutocomplete,
} from "@/Components";

import { Box, Typography } from "@mui/material";
import { router } from "@inertiajs/react";

const AddModulePermission = ({ module, flash, errors, can, sx, onSuccess }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const [form, setForm] = useState({
        type: "",
        module: module, // always set the module value from props
        // add other form fields as needed
    });

    // convert a string to kebab-case
    const toKebabCase = (value) =>
        (value ?? "")
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({
            type: "",
            module: module, // always set the module value from props
            // add other form fields as needed
        });
    };

    const canCreate = can.includes("create-permissions");
    const handleSubmit = (event) => {
        event.preventDefault();

        if (!canCreate) {
            return;
        }

        // set type to kebab-case before submission
        form.type = toKebabCase(form.type);

        // submission here
        router.post("/permissions", form, {
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

    const handleChange = (field) => (e) => {
        setForm((prev) => ({
            ...prev,
            [field]: e.target.value,
        }));
    };

    // convert snake_case to Title Case for display
    const moduleName = module
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");

    const permissionTypes = ["view", "create", "update", "delete"];

    const [formErrors, setFormErrors] = useState(errors ?? {});
    useEffect(() => {
        setFormErrors(errors ?? {});
    }, [errors]);

    // empty errors if modal is closed
    useEffect(() => {
        if (!open) {
            setFormErrors({});
        }
    }, [open]);

    return (
        <>
            {canCreate && (
                <CButtonAdd
                    sx={{
                        fontWeight: 700,
                        minWidth: 32,
                    }}
                    onClick={() => setOpen(true)}
                >
                    New
                </CButtonAdd>
            )}

            <CModal
                title={`Add "${moduleName}" Permission`}
                titleIcon="AddIcon"
                width={450}
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <CFormRow>
                        <CFormGrid size={{ xs: 12 }}>
                            <CAutocomplete
                                freeSolo
                                label="Permission Type"
                                id="type"
                                name="type"
                                value={form.type}
                                onChange={handleChange("type")}
                                onInputChange={(_, value) => {
                                    setForm((prev) => ({
                                        ...prev,
                                        type: toKebabCase(value),
                                    }));
                                }}
                                error={!!formErrors.type}
                                helperText={formErrors.type}
                                placeholder="Enter permission type"
                                slotProps={{
                                    htmlInput: {
                                        maxLength: 15,
                                    },
                                }}
                                options={permissionTypes}
                            />

                            {/* <Typography
                                variant="caption"
                                color="text.secondary"
                            >
                                Result: {toKebabCase(form.type)}
                            </Typography> */}
                        </CFormGrid>
                    </CFormRow>

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

export default AddModulePermission;
