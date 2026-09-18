import Autocomplete from "@mui/material/Autocomplete";
import TextField from "@mui/material/TextField";

const CAutocomplete = ({
    options = [],
    name,
    label,
    value = null,
    onChange,
    error = false,
    helperText = "",
    getOptionLabel,
    isOptionEqualToValue,
    isReadonly = false,
    tabIndex,
    ...props
}) => {
    const defaultGetOptionLabel = (option) => {
        if (typeof option === "string") return option;
        if (!option) return "";

        return option.label ?? option.name ?? option.title ?? String(option);
    };

    const defaultIsOptionEqualToValue = (option, value) => {
        if (typeof option === "string" || typeof value === "string") {
            return option === value;
        }

        if (!option || !value) {
            return option === value;
        }

        if ("id" in option && "id" in value) {
            return option.id === value.id;
        }

        return option === value;
    };

    return (
        <Autocomplete
            {...props}
            size="small"
            options={options}
            value={value}
            readOnly={isReadonly}
            getOptionLabel={getOptionLabel ?? defaultGetOptionLabel}
            isOptionEqualToValue={
                isOptionEqualToValue ?? defaultIsOptionEqualToValue
            }
            onChange={(event, newValue) =>
                onChange?.({
                    target: {
                        name,
                        value: newValue,
                    },
                })
            }
            renderInput={(params) => (
                <TextField
                    {...params}
                    variant="outlined"
                    label={label}
                    error={error}
                    helperText={helperText}
                    slotProps={{
                        ...params.slotProps,
                        htmlInput: {
                            ...params.slotProps?.htmlInput,
                            readOnly: isReadonly,
                            ...(tabIndex !== undefined && {
                                tabIndex: isReadonly ? -1 : tabIndex,
                            }),
                        },
                    }}
                />
            )}
        />
    );
};

export default CAutocomplete;
