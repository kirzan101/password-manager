import {
    Box,
    Card,
    CardContent,
    Chip,
    CircularProgress,
    Divider,
    IconButton,
    Stack,
    Tooltip,
    Typography,
} from "@mui/material";
import KeyIcon from "@mui/icons-material/Key";
import ContentCopyIcon from "@mui/icons-material/ContentCopy";
import { CATEGORY_ICONS } from "../categoryIcons";
import EditCredential from "../Actions/EditCredential";
import { formatLabel, propertiesToFields } from "../categoryDefaults";

const CredentialCard = ({
    credential,
    onCopy,
    onCopySecret,
    copying,
    can,
    errors,
    onSuccess,
}) => {
    const Icon = CATEGORY_ICONS[credential.category] || KeyIcon;
    const fields = propertiesToFields(credential.properties);
    const data = Object.fromEntries(fields.map((f) => [f.key, f.value]));
    const pinned = fields.filter((f) => f.shown).map((f) => f.key);
    // fall back to every field when none are pinned
    const showed = pinned.length > 0 ? pinned : fields.map((f) => f.key);

    return (
        <Card
            elevation={0}
            sx={{
                height: "100%",
                border: "1px solid",
                borderColor: "divider",
                borderRadius: 4,
                backgroundColor: "background.paper",
                overflow: "hidden",
                transition: "all 0.2s ease",
                "&:hover": {
                    borderColor: "primary.main",
                    boxShadow: (theme) =>
                        `0 8px 30px ${theme.palette.primary.main}14`,
                    transform: "translateY(-2px)",
                },
            }}
        >
            <CardContent sx={{ p: 0 }}>
                <Box
                    sx={{
                        px: 2.5,
                        py: 2,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-between",
                        gap: 2,
                        backgroundColor: "action.hover",
                    }}
                >
                    <Stack
                        direction="row"
                        spacing={1.5}
                        sx={{ minWidth: 0, alignItems: "center" }}
                    >
                        <Box
                            sx={{
                                width: 40,
                                height: 40,
                                flexShrink: 0,
                                display: "grid",
                                placeItems: "center",
                                borderRadius: 2,
                                color: "primary.main",
                            }}
                        >
                            <Icon />
                        </Box>
                        <Box sx={{ minWidth: 0 }}>
                            <Typography
                                variant="subtitle1"
                                fontWeight={700}
                                noWrap
                                title={credential.name}
                            >
                                {credential.name}
                            </Typography>
                            <Typography
                                variant="caption"
                                color="text.secondary"
                                noWrap
                            >
                                {credential.username}
                            </Typography>
                        </Box>
                    </Stack>

                    <Chip
                        size="small"
                        label={credential.category || "uncategorized"}
                        sx={{ textTransform: "capitalize", fontWeight: 600 }}
                    />
                </Box>

                <Divider />

                <Stack spacing={0}>
                    {showed.length > 0 ? (
                        showed.map((key) => (
                            <Box
                                key={key}
                                sx={{
                                    px: 2.5,
                                    py: 1.25,
                                    display: "flex",
                                    justifyContent: "space-between",
                                    alignItems: "center",
                                    gap: 2,
                                    "&:hover": {
                                        backgroundColor: "action.hover",
                                    },
                                }}
                            >
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    {formatLabel(key)}
                                </Typography>
                                <Stack
                                    direction="row"
                                    spacing={0.5}
                                    sx={{ alignItems: "center", minWidth: 0 }}
                                >
                                    <Typography
                                        variant="body2"
                                        fontWeight={600}
                                        noWrap
                                    >
                                        {String(data[key] || "—")}
                                    </Typography>
                                    {data[key] && (
                                        <Tooltip title="Copy">
                                            <IconButton
                                                size="small"
                                                onClick={() =>
                                                    onCopy(
                                                        String(data[key]),
                                                        formatLabel(key),
                                                    )
                                                }
                                            >
                                                <ContentCopyIcon fontSize="inherit" />
                                            </IconButton>
                                        </Tooltip>
                                    )}
                                </Stack>
                            </Box>
                        ))
                    ) : (
                        <Box sx={{ px: 2.5, py: 3, textAlign: "center" }}>
                            <Typography variant="body2" color="text.secondary">
                                No details
                            </Typography>
                        </Box>
                    )}
                </Stack>

                <Divider />

                <Box
                    sx={{
                        px: 2.5,
                        py: 1,
                        display: "flex",
                        justifyContent: "flex-end",
                    }}
                >
                    <EditCredential
                        credential={credential}
                        can={can}
                        errors={errors}
                        onSuccess={onSuccess}
                    />
                    <Tooltip title="Copy secret">
                        <span>
                            <IconButton
                                size="small"
                                disabled={copying}
                                onClick={() => onCopySecret(credential.id)}
                            >
                                {copying ? (
                                    <CircularProgress size={18} />
                                ) : (
                                    <KeyIcon fontSize="small" />
                                )}
                            </IconButton>
                        </span>
                    </Tooltip>
                </Box>
            </CardContent>
        </Card>
    );
};

export default CredentialCard;
