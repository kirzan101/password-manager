import { CPasswordField, CFormRow, CFormGrid } from "@/Components";

const FormChangePassword = ({ form, setForm, errors }) => {
    const handleChange = (field) => (e) => {
        setForm((prev) => ({
            ...prev,
            [field]: e.target.value,
        }));
    };
    return (
        <CFormRow>
            <CFormGrid size={{ xs: 12 }}>
                <CPasswordField
                    id="current-password"
                    fullWidth
                    label="Current Password"
                    sx={{ mt: 2 }}
                    value={form.current_password}
                    placeholder="Enter your current password"
                    onChange={handleChange("current_password")}
                    error={!!errors.current_password}
                    helperText={errors.current_password}
                    autoComplete="current-password"
                />

                <CPasswordField
                    id="new-password"
                    fullWidth
                    label="New Password"
                    sx={{ mt: 2 }}
                    value={form.new_password}
                    placeholder="Enter your new password"
                    onChange={handleChange("new_password")}
                    error={!!errors.new_password}
                    helperText={errors.new_password}
                    autoComplete="current-password"
                />

                <CPasswordField
                    id="confirm-password"
                    fullWidth
                    label="Confirm Password"
                    sx={{ mt: 2 }}
                    value={form.confirm_password}
                    placeholder="Confirm your new password"
                    onChange={handleChange("confirm_password")}
                    error={!!errors.confirm_password}
                    helperText={errors.confirm_password}
                    autoComplete="current-password"
                />
            </CFormGrid>
        </CFormRow>
    );
};

export default FormChangePassword;
