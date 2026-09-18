import {
    CTextField,
    CFormGrid,
    CFormRow,
    CSelect,
    CSelectIcon,
} from "@/Components";
import { Grid, Select } from "@mui/material";
import { useState } from "react";

import { iconMap } from "@/Utilities/icons";

const FormModule = ({ form, setForm, errors = {}, categories }) => {
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
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CSelectIcon
                    label="Icon"
                    id="icon"
                    name="icon"
                    value={form.icon}
                    onChange={handleChange("icon")}
                    error={!!errors.icon}
                    helperText={errors.icon}
                    options={Object.keys(iconMap).map((key) => ({
                        value: key,
                        label: key,
                    }))}
                    iconMap={iconMap}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Route"
                    id="route"
                    name="route"
                    value={form.route}
                    onChange={handleChange("route")}
                    error={!!errors.route}
                    helperText={errors.route}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CSelect
                    label="Category"
                    id="category"
                    name="category"
                    value={form.category}
                    onChange={handleChange("category")}
                    error={!!errors.category}
                    helperText={errors.category}
                    options={categories.map((category) => ({
                        value: category,
                        label: category,
                    }))}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Order"
                    id="order"
                    name="order"
                    value={form.order}
                    onChange={handleChange("order")}
                    error={!!errors.order}
                    helperText={errors.order}
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormModule;
