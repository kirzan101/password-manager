import { Search } from "@mui/icons-material";
import { TextField, InputAdornment } from "@mui/material";

const CSearchField = ({ children, ...props }) => (
    <TextField
        id="search"
        fullWidth
        size="small"
        variant="outlined"
        color="textField"
        placeholder="Type to search..."
        slotProps={{
            input: {
                startAdornment: (
                    <InputAdornment position="start">
                        <Search />
                    </InputAdornment>
                ),
            },
        }}
        {...props}
    >
        {children}
    </TextField>
);

export default CSearchField;
