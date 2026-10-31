import {
    CModal,
    CButtonAdd,
    CButtonClose,
    CButtonSubmit,
    CFormRow,
    CFormGrid,
    CSelect,
} from "@/Components";
import { useState } from "react";

import FormCredential from "./Forms/FormCredential";
import { Box, ToggleButton, ToggleButtonGroup } from "@mui/material";
import { CATEGORY_ICONS } from "../categoryIcons";
import {
    defaultFields,
    fieldsToProperties,
    validateFields,
    validateMain,
} from "../categoryDefaults";
import { router } from "@inertiajs/react";

const AddCredential = ({ flash, errors, can, sx, onSuccess, categories }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const [fields, setFields] = useState([]);
    const [fieldErrors, setFieldErrors] = useState({});
    const [mainErrors, setMainErrors] = useState({});
    const [form, setForm] = useState({
        name: "",
        username: "",
        secret: "",
        notes: "",
        category: "",
        properties: {},
    });

    // reset form value to initial state
    const handleResetForm = () => {
        setForm({
            name: "",
            username: "",
            secret: "",
            notes: "",
            category: "",
            properties: {},
        });
        setFields([]);
        setFieldErrors({});
        setMainErrors({});
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        const invalid = validateFields(fields);
        const invalidMain = validateMain(form);
        setFieldErrors(invalid);
        setMainErrors(invalidMain);
        if (
            Object.keys(invalid).length > 0 ||
            Object.keys(invalidMain).length > 0
        )
            return;

        // submission here
        router.post(
            "/credentials",
            { ...form, properties: fieldsToProperties(fields) },
            {
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
            },
        );
    };

    const canCreate = can.includes("create-credentials");

    const handleCategoryChange = (category) => {
        if (!category) return;

        setForm((prev) => ({ ...prev, category }));
        setMainErrors((prev) => ({ ...prev, category: undefined }));
        setFields(defaultFields(category));
        setFieldErrors({});
    };

    return (
        <>
            {canCreate && <CButtonAdd sx={sx} onClick={() => setOpen(true)} />}

            <CModal
                title="Add Credential"
                titleIcon="AddIcon"
                width={600}
                open={open}
                onClose={() => setOpen(false)}
            >
                <form onSubmit={handleSubmit}>
                    <CFormRow sx={{ mb: 2 }}>
                        <CFormGrid size={{ xs: 12 }}>
                            <ToggleButtonGroup
                                exclusive
                                fullWidth
                                size="small"
                                color="primary"
                                value={form.category || null}
                                onChange={(_, value) =>
                                    handleCategoryChange(value)
                                }
                                sx={{ flexWrap: "wrap", gap: 1 }}
                            >
                                {categories.map((cat) => {
                                    const Icon = CATEGORY_ICONS[cat];
                                    return (
                                        <ToggleButton
                                            key={cat}
                                            value={cat}
                                            sx={{
                                                flex: "1 1 30%",
                                                flexDirection: "column",
                                                textTransform: "capitalize",
                                                borderRadius: 2,
                                                border: "1px solid",
                                                borderColor: "divider",
                                            }}
                                        >
                                            {Icon && <Icon />}
                                            {cat}
                                        </ToggleButton>
                                    );
                                })}
                            </ToggleButtonGroup>
                            {(mainErrors.category ||
                                (errors.category && !form.category)) && (
                                <Box sx={{ color: "error.main", fontSize: 12 }}>
                                    {mainErrors.category || errors.category}
                                </Box>
                            )}
                        </CFormGrid>
                    </CFormRow>

                    <FormCredential
                        form={form}
                        setForm={setForm}
                        fields={fields}
                        setFields={setFields}
                        fieldErrors={fieldErrors}
                        errors={{ ...errors, ...mainErrors }}
                        isReadonly={false}
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

export default AddCredential;
