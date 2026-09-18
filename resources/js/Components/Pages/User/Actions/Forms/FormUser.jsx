import {
    CTextField,
    CFormGrid,
    CFormRow,
    CSelect,
    CAutocomplete,
} from "@/Components";
import { Autocomplete, Grid, Typography } from "@mui/material";
import { useState } from "react";

const FormUser = ({
    form,
    setForm,
    errors = {},
    userGroups,
    accountTypes,
    roles,
    can,
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
            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="First name"
                    id="first_name"
                    name="first_name"
                    value={form.first_name}
                    onChange={handleChange("first_name")}
                    error={!!errors.first_name}
                    helperText={errors.first_name}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Middle name"
                    id="middle_name"
                    name="middle_name"
                    value={form.middle_name}
                    onChange={handleChange("middle_name")}
                    error={!!errors.middle_name}
                    helperText={errors.middle_name}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Last name"
                    id="last_name"
                    name="last_name"
                    value={form.last_name}
                    onChange={handleChange("last_name")}
                    error={!!errors.last_name}
                    helperText={errors.last_name}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Nickname"
                    id="nickname"
                    name="nickname"
                    value={form.nickname}
                    onChange={handleChange("nickname")}
                    error={!!errors.nickname}
                    helperText={errors.nickname}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CSelect
                    label="Account Type"
                    id="type"
                    name="type"
                    value={form.type}
                    onChange={handleChange("type")}
                    error={!!errors.type}
                    helperText={errors.type}
                    options={accountTypes.map((type) => ({
                        value: type,
                        label: type,
                    }))}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Position"
                    id="position"
                    name="position"
                    value={form.position}
                    onChange={handleChange("position")}
                    error={!!errors.position}
                    helperText={errors.position}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 12 }}>
                <Autocomplete
                    multiple
                    freeSolo
                    size="small"
                    options={[]}
                    value={form.contact_numbers}
                    readOnly={isReadonly}
                    onChange={(_, value) =>
                        setForm((prev) => ({
                            ...prev,
                            contact_numbers: value,
                        }))
                    }
                    renderInput={(params) => (
                        <CTextField
                            {...params}
                            label="Contact Numbers"
                            error={!!errors.contact_numbers}
                            helperText={
                                errors.contact_numbers ??
                                "Hit enter after typing a number to add it to the list."
                            }
                            isReadonly={isReadonly}
                        />
                    )}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 6 }}>
                <CSelect
                    label="User Group"
                    id="user_group_id"
                    name="user_group_id"
                    value={form.user_group_id}
                    onChange={handleChange("user_group_id")}
                    error={!!errors.user_group_id}
                    helperText={errors.user_group_id}
                    options={userGroups.map((group) => ({
                        value: group.id,
                        label: group.name,
                    }))}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 6 }}>
                <CAutocomplete
                    multiple
                    label="Roles"
                    name="role_ids"
                    value={roles
                        .filter((role) => form.role_ids.includes(role.id))
                        .map((role) => ({
                            value: role.id,
                            label: role.name,
                        }))}
                    onChange={(e) =>
                        handleChange("role_ids")({
                            target: {
                                name: "role_ids",
                                value: e.target.value.map((role) => role.value),
                            },
                        })
                    }
                    error={!!errors.role_ids}
                    helperText={errors.role_ids}
                    options={roles.map((role) => ({
                        value: role.id,
                        label: role.name,
                    }))}
                    getOptionLabel={(option) => option.label ?? ""}
                    isOptionEqualToValue={(option, value) =>
                        option.value === value.value
                    }
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 6 }}>
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

            <CFormGrid size={{ xs: 12, sm: 6 }}>
                <CTextField
                    label="Email"
                    id="email"
                    name="email"
                    value={form.email}
                    onChange={handleChange("email")}
                    error={!!errors.email}
                    helperText={errors.email}
                    isReadonly={isReadonly}
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormUser;
