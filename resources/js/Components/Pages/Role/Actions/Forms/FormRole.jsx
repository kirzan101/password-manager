import { CTextField, CFormGrid, CFormRow, CSwitchLabeled } from "@/Components";
import { Grid } from "@mui/material";
import { useState } from "react";

const FormRole = ({
    form,
    setForm,
    errors = {},
    permissions,
    moduleLists,
    isReadonly = false,
}) => {
    const handleChange = (field) => (e) => {
        setForm((prev) => ({
            ...prev,
            [field]: e.target.value,
        }));
    };

    return (
        <CFormRow>
            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Name"
                    id="name"
                    name="name"
                    value={form.name}
                    onChange={handleChange("name")}
                    error={!!errors.name}
                    helperText={errors.name}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Description"
                    id="description"
                    name="description"
                    value={form.description}
                    onChange={handleChange("description")}
                    error={!!errors.description}
                    helperText={errors.description}
                    multiline
                    rows={4}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CSwitchLabeled
                    label="Active"
                    checked={form.is_active}
                    onChange={(e) =>
                        setForm((prev) => ({
                            ...prev,
                            is_active: e.target.checked,
                        }))
                    }
                    isReadonly={isReadonly}
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormRole;
