import {
    Box,
    Button,
    IconButton,
    LinearProgress,
    Stack,
    TextField,
    Tooltip,
    Typography,
} from "@mui/material";
import AddIcon from "@mui/icons-material/Add";
import DeleteOutlinedIcon from "@mui/icons-material/DeleteOutlineOutlined";
import StarIcon from "@mui/icons-material/Star";
import StarBorderIcon from "@mui/icons-material/StarBorder";

const FormProperties = ({
    fields,
    setFields,
    errors = {},
    fieldErrors = {},
}) => {
    const update = (id, patch) =>
        setFields((prev) =>
            prev.map((f) => (f.id === id ? { ...f, ...patch } : f)),
        );

    const addField = () =>
        setFields((prev) => [
            ...prev,
            { id: `custom-${Date.now()}`, key: "", value: "", shown: false },
        ]);

    const removeField = (id) =>
        setFields((prev) => prev.filter((f) => f.id !== id));

    const filled = fields.filter((f) => f.value.trim() !== "").length;
    const progress = fields.length ? (filled / fields.length) * 100 : 0;

    return (
        <Box
            sx={{
                p: 2,
                border: "1px solid",
                borderColor: "divider",
                borderRadius: 3,
            }}
        >
            <Stack
                direction="row"
                sx={{ justifyContent: "space-between", mb: 0.5 }}
            >
                <Typography variant="subtitle2" fontWeight={700}>
                    Details
                </Typography>
                <Typography variant="caption" color="text.secondary">
                    {filled}/{fields.length} filled
                </Typography>
            </Stack>
            <LinearProgress
                variant="determinate"
                value={progress}
                sx={{ mb: 2, height: 8, borderRadius: 4 }}
            />

            <Stack spacing={1.5}>
                {fields.map((field) => (
                    <Stack
                        key={field.id}
                        direction="row"
                        spacing={1}
                        sx={{ alignItems: "flex-start" }}
                    >
                        <TextField
                            size="small"
                            label="Field"
                            error={!!fieldErrors[field.id]?.key}
                            helperText={fieldErrors[field.id]?.key}
                            value={field.key}
                            onChange={(e) =>
                                update(field.id, { key: e.target.value })
                            }
                            sx={{ width: "35%" }}
                        />
                        <TextField
                            size="small"
                            label="Value"
                            value={field.value}
                            onChange={(e) =>
                                update(field.id, { value: e.target.value })
                            }
                            error={
                                !!fieldErrors[field.id]?.value ||
                                !!errors[`properties.data.${field.key}`]
                            }
                            helperText={
                                fieldErrors[field.id]?.value ||
                                errors[`properties.data.${field.key}`]
                            }
                            sx={{ flex: 1 }}
                        />
                        <Tooltip
                            title={
                                field.shown
                                    ? "Shown on card"
                                    : "Pin to show on card"
                            }
                        >
                            <IconButton
                                size="small"
                                color={field.shown ? "warning" : "default"}
                                onClick={() =>
                                    update(field.id, { shown: !field.shown })
                                }
                            >
                                {field.shown ? (
                                    <StarIcon />
                                ) : (
                                    <StarBorderIcon />
                                )}
                            </IconButton>
                        </Tooltip>
                        <Tooltip title="Remove field">
                            <IconButton
                                size="small"
                                onClick={() => removeField(field.id)}
                            >
                                <DeleteOutlinedIcon />
                            </IconButton>
                        </Tooltip>
                    </Stack>
                ))}
            </Stack>

            <Button
                size="small"
                startIcon={<AddIcon />}
                onClick={addField}
                sx={{ mt: 1.5 }}
            >
                Add field
            </Button>
        </Box>
    );
};

export default FormProperties;
