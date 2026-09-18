import {
    Box,
    Checkbox,
    Chip,
    FormControl,
    InputLabel,
    ListItemText,
    MenuItem,
    Select,
} from "@mui/material";

const CSelectMultiple = ({
    options = [],
    name,
    label,
    value = [],
    onChange,
    error,
    helperText,
    ...props
}) => {
    const labelId = `${name}-label`;

    const handleDelete = (chipValue) => {
        const newValue = value.filter((v) => v !== chipValue);

        onChange?.({
            target: {
                name,
                value: newValue,
            },
        });
    };

    return (
        <FormControl fullWidth size="small" error={error}>
            <InputLabel id={labelId}>{label}</InputLabel>

            <Select
                labelId={labelId}
                name={name}
                multiple
                value={value}
                label={label}
                onChange={onChange}
                color="textField"
                renderValue={(selected) => (
                    <Box
                        sx={{
                            display: "flex",
                            flexWrap: "wrap",
                            gap: 0.5,
                        }}
                    >
                        {selected.map((selectedValue) => {
                            const option = options.find(
                                (o) => o.value === selectedValue,
                            );

                            return (
                                <Chip
                                    key={selectedValue}
                                    label={
                                        option?.label ?? String(selectedValue)
                                    }
                                    size="small"
                                    onDelete={() => handleDelete(selectedValue)}
                                    onMouseDown={(e) => {
                                        // prevents opening dropdown when clicking delete
                                        e.stopPropagation();
                                    }}
                                />
                            );
                        })}
                    </Box>
                )}
                {...props}
            >
                {options.map((option) => (
                    <MenuItem key={option.value} value={option.value}>
                        <Checkbox
                            size="small"
                            checked={value.includes(option.value)}
                        />

                        <ListItemText primary={option.label} />
                    </MenuItem>
                ))}
            </Select>

            {helperText && (
                <Box
                    sx={{
                        mt: 0.5,
                        ml: 1.75,
                        fontSize: "0.75rem",
                        color: error ? "error.main" : "text.secondary",
                    }}
                >
                    {helperText}
                </Box>
            )}
        </FormControl>
    );
};

export default CSelectMultiple;
