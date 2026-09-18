import { MenuItem } from "@mui/material";
import CTextField from "../TextFields/CTextField";

const CSelect = ({
    options,
    value,
    onChange,
    label,
    error,
    isReadonly = false,
    tabIndex,
    slotProps = {},
}) => {
    return (
        <CTextField
            select
            fullWidth
            label={label}
            value={value}
            onChange={onChange}
            error={!!error}
            helperText={error}
            isReadonly={isReadonly}
            tabIndex={tabIndex}
            slotProps={slotProps}
        >
            {options.map((option) => (
                <MenuItem key={option.value} value={option.value}>
                    {option.label}
                </MenuItem>
            ))}
        </CTextField>
    );
};

export default CSelect;
