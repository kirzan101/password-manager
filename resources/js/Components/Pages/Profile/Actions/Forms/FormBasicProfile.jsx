import { CTextField, CFormGrid, CFormRow } from "@/Components";
import { Autocomplete } from "@mui/material";

const FormBasicProfile = ({ form, setForm, errors, profile }) => {
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
                    value={profile.first_name ?? ""}
                    disabled
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Middle name"
                    id="middle_name"
                    name="middle_name"
                    value={profile.middle_name ?? ""}
                    disabled
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 4 }}>
                <CTextField
                    label="Last name"
                    id="last_name"
                    name="last_name"
                    value={profile.last_name ?? ""}
                    disabled
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 6 }}>
                <CTextField
                    label="Nickname"
                    id="nickname"
                    name="nickname"
                    value={form.nickname}
                    onChange={handleChange("nickname")}
                    error={!!errors.nickname}
                    helperText={errors.nickname}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 6 }}>
                <CTextField
                    label="Position"
                    id="position"
                    name="position"
                    value={form.position}
                    onChange={handleChange("position")}
                    error={!!errors.position}
                    helperText={errors.position}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 12 }}>
                <CTextField
                    label="Email"
                    id="email"
                    name="email"
                    value={form.email}
                    onChange={handleChange("email")}
                    error={!!errors.email}
                    helperText={errors.email}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12, sm: 12 }}>
                <Autocomplete
                    multiple
                    freeSolo
                    size="small"
                    options={[]}
                    value={form.contact_numbers}
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
                        />
                    )}
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormBasicProfile;
