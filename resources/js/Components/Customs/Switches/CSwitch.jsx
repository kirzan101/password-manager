import Switch from "@mui/material/Switch";

const CSwitch = ({ slotProps, ...props }) => {
    return <Switch color="primary" slotProps={slotProps} {...props} />;
};

export default CSwitch;
