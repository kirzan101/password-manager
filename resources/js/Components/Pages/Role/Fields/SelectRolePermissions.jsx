import { useMemo, useState } from "react";
import {
    Alert,
    Box,
    Checkbox,
    Divider,
    FormControlLabel,
    FormGroup,
    Grid,
    Typography,
    InputAdornment,
} from "@mui/material";
import { CCard, CCardContent, CTextField } from "@/Components";
import SearchIcon from "@mui/icons-material/Search";

const PERMISSION_TYPE_ORDER = ["view", "create", "update", "delete"];

const formatLabel = (value = "") =>
    value
        .toString()
        .replace(/_/g, " ")
        .replace(/\b\w/g, (char) => char.toUpperCase());

/**
 * Field used to assign permissions to a role.
 *
 * Permissions are grouped by their module (e.g. "User Group", "User") so
 * every permission type (Create, Edit, View, Delete) belonging to that
 * module is displayed together as checkboxes. Selecting/deselecting a
 * permission simply adds/removes its id from the `permissionIds` array,
 * which the backend then translates into creating/deleting the
 * corresponding role_permission record (no `is_active` flag involved).
 */
const SelectRolePermissions = ({
    selectedPermissions = [],
    onChange,
    permissions = [],
    moduleLists = [],
    errors = {},
    isReadonly = false,
}) => {
    const groupedPermissions = useMemo(() => {
        const moduleOrder = new Map(
            moduleLists
                .map((module) => module.name || module)
                .sort((a, b) => a.localeCompare(b))
                .map((module, index) => [
                    module.toLowerCase().replace(/\s+/g, "_"),
                    index,
                ]),
        );

        const groups = new Map();

        permissions
            .filter((permission) => permission.is_active)
            .forEach((permission) => {
                if (!groups.has(permission.module)) {
                    groups.set(permission.module, []);
                }

                groups.get(permission.module).push(permission);
            });

        return Array.from(groups.entries())
            .sort(
                ([moduleA], [moduleB]) =>
                    (moduleOrder.get(moduleA) ?? Infinity) -
                    (moduleOrder.get(moduleB) ?? Infinity),
            )
            .map(([module, modulePermissions]) => ({
                module,
                label: formatLabel(module),
                permissions: modulePermissions.sort(
                    (a, b) =>
                        PERMISSION_TYPE_ORDER.indexOf(a.type) -
                        PERMISSION_TYPE_ORDER.indexOf(b.type),
                ),
            }));
    }, [permissions, moduleLists]);

    const handleTogglePermission = (permissionId) => (event) => {
        const { checked } = event.target;

        if (checked) {
            onChange([...selectedPermissions, permissionId]);
        } else {
            onChange(selectedPermissions.filter((id) => id !== permissionId));
        }
    };

    const handleToggleGroup = (modulePermissions) => (event) => {
        const { checked } = event.target;
        const modulePermissionIds = modulePermissions.map(
            (permission) => permission.id,
        );

        if (checked) {
            onChange([
                ...selectedPermissions,
                ...modulePermissionIds.filter(
                    (id) => !selectedPermissions.includes(id),
                ),
            ]);
        } else {
            onChange(
                selectedPermissions.filter(
                    (id) => !modulePermissionIds.includes(id),
                ),
            );
        }
    };

    // implementation of module search functionality
    const [moduleSearch, setModuleSearch] = useState("");

    const filteredGroupedPermissions = useMemo(() => {
        const search = moduleSearch.trim().toLowerCase();

        if (!search) return groupedPermissions;

        return groupedPermissions.filter(({ label, module }) =>
            `${label} ${module}`.toLowerCase().includes(search),
        );
    }, [groupedPermissions, moduleSearch]);

    return (
        <Box>
            <Typography variant="h6" sx={{ mb: 2 }}>
                Permissions
            </Typography>

            {errors?.permissionIds && (
                <Alert severity="error" sx={{ mb: 2 }}>
                    {errors.permissionIds}
                </Alert>
            )}

            <CTextField
                fullWidth
                size="small"
                placeholder="Search modules..."
                value={moduleSearch}
                onChange={(e) => setModuleSearch(e.target.value)}
                slotProps={{
                    input: {
                        startAdornment: (
                            <InputAdornment position="start">
                                <SearchIcon fontSize="small" />
                            </InputAdornment>
                        ),
                    },
                }}
                sx={{ mb: 2 }}
            />

            <Grid container spacing={2}>
                {filteredGroupedPermissions.map(
                    ({ module, label, permissions: modulePermissions }) => {
                        const modulePermissionIds = modulePermissions.map(
                            (permission) => permission.id,
                        );
                        const selectedCount = modulePermissionIds.filter((id) =>
                            selectedPermissions.includes(id),
                        ).length;
                        const allSelected =
                            modulePermissionIds.length > 0 &&
                            selectedCount === modulePermissionIds.length;
                        const someSelected = selectedCount > 0 && !allSelected;

                        return (
                            <Grid key={module} size={{ xs: 12, sm: 4 }}>
                                <CCard
                                    elevation={0}
                                    sx={{
                                        height: "100%",
                                        border: "1px solid",
                                        borderColor: "divider",
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
                                    <CCardContent>
                                        <Box
                                            sx={{
                                                display: "flex",
                                                alignItems: "center",
                                                justifyContent: "space-between",
                                                mb: 1,
                                            }}
                                        >
                                            <Typography
                                                variant="subtitle1"
                                                sx={{ fontWeight: "bold" }}
                                            >
                                                {label}
                                            </Typography>

                                            <FormControlLabel
                                                sx={{ mr: 0 }}
                                                control={
                                                    <Checkbox
                                                        size="small"
                                                        checked={allSelected}
                                                        indeterminate={
                                                            someSelected
                                                        }
                                                        onChange={
                                                            isReadonly
                                                                ? undefined
                                                                : handleToggleGroup(
                                                                      modulePermissions,
                                                                  )
                                                        }
                                                    />
                                                }
                                                label="Select All"
                                            />
                                        </Box>

                                        <Divider sx={{ mb: 1 }} />

                                        <FormGroup>
                                            {modulePermissions.map(
                                                (permission) => (
                                                    <FormControlLabel
                                                        key={permission.id}
                                                        control={
                                                            <Checkbox
                                                                size="small"
                                                                checked={selectedPermissions.includes(
                                                                    permission.id,
                                                                )}
                                                                onChange={
                                                                    isReadonly
                                                                        ? undefined
                                                                        : handleTogglePermission(
                                                                              permission.id,
                                                                          )
                                                                }
                                                            />
                                                        }
                                                        label={`${label} - ${formatLabel(
                                                            permission.type,
                                                        )}`}
                                                    />
                                                ),
                                            )}
                                        </FormGroup>
                                    </CCardContent>
                                </CCard>
                            </Grid>
                        );
                    },
                )}
            </Grid>

            {!errors?.permissionIds && (
                <Typography
                    variant="caption"
                    color="text.secondary"
                    sx={{ mt: 1, display: "block" }}
                >
                    Select the permissions to grant to this role.
                </Typography>
            )}
        </Box>
    );
};

export default SelectRolePermissions;
