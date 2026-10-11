import {
    CTextField,
    CFormGrid,
    CFormRow,
    CSelect,
    CPasswordField,
} from "@/Components";
import { Grid } from "@mui/material";
import { useState } from "react";

const FormCredential = ({ form, setForm, errors = {}, isReadonly = false }) => {
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
                    label="Username"
                    id="username"
                    name="username"
                    value={form.username}
                    onChange={handleChange("username")}
                    error={!!errors.username}
                    helperText={errors.username}
                    isReadonly={isReadonly}
                />
            </CFormGrid>
            <CFormGrid size={{ xs: 12 }}>
                <CPasswordField
                    label="Secret"
                    id="secret"
                    name="secret"
                    value={form.secret}
                    onChange={handleChange("secret")}
                    error={!!errors.secret}
                    helperText={errors.secret}
                    isReadonly={isReadonly}
                />
            </CFormGrid>
            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Notes"
                    id="notes"
                    name="notes"
                    value={form.notes}
                    onChange={handleChange("notes")}
                    error={!!errors.notes}
                    helperText={errors.notes}
                    isReadonly={isReadonly}
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormCredential;
