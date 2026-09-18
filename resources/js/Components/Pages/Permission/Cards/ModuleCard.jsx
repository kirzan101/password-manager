import {
    Box,
    Card,
    CardContent,
    Chip,
    Divider,
    Stack,
    Typography,
} from "@mui/material";
import { CButtonAdd, CIconButton } from "@/Components";
import AddModulePermission from "../Actions/AddModulePermission";
import RemoveModulePermission from "../Actions/RemoveModulePermission";

import SecurityIcon from "@mui/icons-material/Security";
import CancelOutlinedIcon from "@mui/icons-material/CancelOutlined";
import CheckCircleOutlineOutlinedIcon from "@mui/icons-material/CheckCircleOutlineOutlined";

const ModuleCard = ({ flash, errors, can, moduleName, permissions = [] }) => {
    // convert snake_case to Title Case
    const formattedModuleName = moduleName
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");

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
                {/* Header */}
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
                        sx={{
                            minWidth: 0,
                            alignItems: "center",
                        }}
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
                                backgroundColor: "primary.50",
                            }}
                        >
                            <SecurityIcon fontSize="medium" color="iconColor" />
                        </Box>

                        <Box sx={{ minWidth: 0 }}>
                            <Typography
                                variant="subtitle1"
                                fontWeight={700}
                                noWrap
                                title={formattedModuleName}
                            >
                                {formattedModuleName}
                            </Typography>

                            <Typography
                                variant="caption"
                                color="text.secondary"
                            >
                                {permissions.length}{" "}
                                {permissions.length === 1
                                    ? "permission"
                                    : "permissions"}
                            </Typography>
                        </Box>
                    </Stack>

                    <AddModulePermission
                        module={moduleName}
                        flash={flash}
                        errors={errors}
                        can={can}
                        onSuccess={() => {
                            // handle success if needed
                        }}
                    />
                </Box>

                <Divider />

                {/* Permissions */}
                <Stack spacing={0}>
                    {permissions.length > 0 ? (
                        permissions.map((permission) => {
                            const isActive = permission.is_active;

                            return (
                                <Box
                                    key={permission.permission_id}
                                    sx={{
                                        px: 2.5,
                                        py: 1.5,
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "space-between",
                                        gap: 2,
                                        transition:
                                            "background-color 0.15s ease",
                                        "&:hover": {
                                            backgroundColor: "action.hover",
                                        },
                                    }}
                                >
                                    {/* Permission name */}
                                    <Box
                                        sx={{
                                            display: "flex",
                                            alignItems: "center",
                                            gap: 1,
                                            minWidth: 0,
                                        }}
                                    >
                                        <Box
                                            sx={{
                                                width: 6,
                                                height: 6,
                                                flex: "0 0 6px",
                                                borderRadius: "50%",
                                                backgroundColor: isActive
                                                    ? "accent.main"
                                                    : "text.disabled",
                                            }}
                                        />

                                        <Typography
                                            variant="body2"
                                            fontWeight={600}
                                            sx={{
                                                lineHeight: 1.5,
                                            }}
                                        >
                                            {permission.type}
                                        </Typography>
                                    </Box>

                                    {/* Status + actions */}
                                    <Stack
                                        direction="row"
                                        spacing={0.5}
                                        sx={{
                                            flexShrink: 0,
                                            alignItems: "center",
                                        }}
                                    >
                                        <Chip
                                            size="small"
                                            icon={
                                                isActive ? (
                                                    <CheckCircleOutlineOutlinedIcon />
                                                ) : (
                                                    <CancelOutlinedIcon />
                                                )
                                            }
                                            label={
                                                isActive ? "Active" : "Inactive"
                                            }
                                            color={
                                                isActive ? "accent" : "default"
                                            }
                                            variant={
                                                isActive ? "filled" : "outlined"
                                            }
                                            sx={{
                                                height: 32,
                                                fontWeight: 600,
                                                "& .MuiChip-icon": {
                                                    fontSize: 16,
                                                },
                                            }}
                                        />

                                        <RemoveModulePermission
                                            permission={permission}
                                            onSuccess={() => {
                                                // handle success
                                            }}
                                            can={can}
                                            errors={errors}
                                        />
                                    </Stack>
                                </Box>
                            );
                        })
                    ) : (
                        <Box
                            sx={{
                                px: 2.5,
                                py: 4,
                                textAlign: "center",
                            }}
                        >
                            <Typography variant="body2" color="text.secondary">
                                No permissions available
                            </Typography>
                        </Box>
                    )}
                </Stack>
            </CardContent>
        </Card>
    );
};

export default ModuleCard;
