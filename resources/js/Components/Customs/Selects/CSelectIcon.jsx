import { CTextField } from "@/Components";
import { MenuItem } from "@mui/material";

const CSelectIcon = ({ options, value, onChange, label, error, iconMap }) => {
    return (
        <CTextField
            select
            fullWidth
            label={label}
            value={value}
            onChange={onChange}
            error={!!error}
            helperText={error}
        >
            {options.map((option) => {
                const Icon = iconMap?.[option.value];

                return (
                    <MenuItem key={option.value} value={option.value}>
                        <div
                            style={{
                                display: "flex",
                                alignItems: "center",
                                gap: 8,
                            }}
                        >
                            {Icon && <Icon fontSize="small" />}
                            {option.label}
                        </div>
                    </MenuItem>
                );
            })}
        </CTextField>
    );
};

export default CSelectIcon;
