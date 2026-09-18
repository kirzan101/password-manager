import Switch from "@mui/material/Switch";
import FormControlLabel from "@mui/material/FormControlLabel";

const CSwitchLabeled = ({
    label = "Label",
    checked,
    onChange,
    isReadonly = false,
}) => {
    return (
        <FormControlLabel
            label={label}
            control={
                <Switch
                    checked={checked}
                    onChange={isReadonly ? undefined : onChange}
                    color="primary"
                    slotProps={{
                        input: {
                            "aria-label": "controlled",
                        },
                    }}
                />
            }
        />
    );
};

export default CSwitchLabeled;
