import {
    CTextField,
    CFormGrid,
    CFormRow,
    CSelect,
    CPasswordField,
} from "@/Components";

const FormDatabase = ({ form, setForm, errors = {}, isReadonly = false }) => {
    const handleChange = (field) => (e) => {
        const value = e.target.value;

        setForm((prev) => ({
            ...prev,
            properties: {
                ...prev.properties,
                [field]: value,
            },
        }));
    };

    return (
        <>
            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Host"
                    id="host"
                    name="host"
                    value={form.properties?.host ?? ""}
                    onChange={handleChange("host")}
                    error={!!errors["properties.host"]}
                    helperText={errors["properties.host"]}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Port"
                    id="port"
                    name="port"
                    value={form.properties?.port ?? ""}
                    onChange={handleChange("port")}
                    error={!!errors["properties.port"]}
                    helperText={errors["properties.port"]}
                    isReadonly={isReadonly}
                />
            </CFormGrid>

            <CFormGrid size={{ xs: 12 }}>
                <CTextField
                    label="Database Name"
                    id="database"
                    name="database"
                    value={form.properties?.database ?? ""}
                    onChange={handleChange("database")}
                    error={!!errors["properties.database"]}
                    helperText={errors["properties.database"]}
                    isReadonly={isReadonly}
                />
            </CFormGrid>
        </>
    );
};

export default FormDatabase;
