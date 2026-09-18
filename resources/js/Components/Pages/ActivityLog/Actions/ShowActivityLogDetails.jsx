import { useMemo, useState } from "react";
import { CModal, CButton, CButtonClose } from "@/Components";

import { Box, Typography, Divider, Chip } from "@mui/material";

const ShowActivityLogDetails = ({ activityLog, sx }) => {
    const [open, setOpen] = useState(false);

    const properties = activityLog.properties || {};
    const oldProperties = activityLog.old_properties || {};

    const hasOldProperties =
        activityLog.old_properties && Object.keys(oldProperties).length > 0;

    /**
     * Convert:
     *
     * first_name -> First Name
     * user_name  -> User Name
     * email      -> Email
     */
    const formatLabel = (key) => {
        return String(key)
            .replace(/_/g, " ")
            .replace(/([a-z])([A-Z])/g, "$1 $2")
            .replace(/\b\w/g, (char) => char.toUpperCase());
    };

    /**
     * Convert values into something user-friendly.
     */
    const formatValue = (value) => {
        if (value === null || value === undefined) {
            return "—";
        }

        if (typeof value === "boolean") {
            return value ? "Yes" : "No";
        }

        if (Array.isArray(value)) {
            if (value.length === 0) {
                return "—";
            }

            return value.map((item) => formatValue(item)).join(", ");
        }

        if (typeof value === "object") {
            return JSON.stringify(value, null, 2);
        }

        return String(value);
    };

    /**
     * Get all unique property names from old + new.
     */
    const comparison = useMemo(() => {
        const keys = new Set([
            ...Object.keys(oldProperties),
            ...Object.keys(properties),
        ]);

        return [...keys].map((key) => {
            const oldExists = Object.prototype.hasOwnProperty.call(
                oldProperties,
                key,
            );

            const newExists = Object.prototype.hasOwnProperty.call(
                properties,
                key,
            );

            const oldValue = oldProperties[key];
            const newValue = properties[key];

            let type = "unchanged";

            if (!oldExists && newExists) {
                type = "added";
            } else if (oldExists && !newExists) {
                type = "removed";
            } else if (JSON.stringify(oldValue) !== JSON.stringify(newValue)) {
                type = "changed";
            }

            return {
                key,
                label: formatLabel(key),
                oldValue,
                newValue,
                oldExists,
                newExists,
                type,
            };
        });
    }, [oldProperties, properties]);

    /**
     * Render a single value.
     */
    const ValueDisplay = ({ value, exists = true, changed = false }) => {
        if (!exists) {
            return (
                <Typography
                    variant="body2"
                    sx={{
                        color: "error.main",
                        fontStyle: changed ? "italic" : "normal",
                    }}
                >
                    Removed
                </Typography>
            );
        }

        const isObject = value !== null && typeof value === "object";

        if (isObject) {
            return (
                <Box
                    component="pre"
                    sx={{
                        m: 0,
                        fontFamily: '"Roboto Mono", "Consolas", monospace',
                        fontSize: "0.8rem",
                        whiteSpace: "pre-wrap",
                        wordBreak: "break-word",
                        color: "text.primary",
                        fontStyle: changed ? "italic" : "normal",
                    }}
                >
                    {formatValue(value)}
                </Box>
            );
        }

        return (
            <Typography
                variant="body2"
                sx={{
                    wordBreak: "break-word",
                    color: "text.primary",
                    fontStyle: changed ? "italic" : "normal",
                }}
            >
                {formatValue(value)}
            </Typography>
        );
    };

    /**
     * Normal property display when there is no old_properties.
     */
    const PropertyList = () => {
        if (comparison.length === 0) {
            return (
                <Typography
                    variant="body2"
                    color="text.secondary"
                    sx={{ py: 3, textAlign: "center" }}
                >
                    No properties available.
                </Typography>
            );
        }

        return (
            <Box
                sx={{
                    border: 1,
                    borderColor: "divider",
                    borderRadius: 1,
                    overflow: "hidden",
                }}
            >
                {comparison.map((item, index) => (
                    <Box key={item.key}>
                        <Box
                            sx={{
                                display: "grid",
                                gridTemplateColumns: {
                                    xs: "1fr",
                                    sm: "180px 1fr",
                                },
                                px: 2,
                                py: 1.5,
                                gap: 2,
                            }}
                        >
                            <Typography
                                variant="body2"
                                fontWeight={600}
                                color="text.secondary"
                            >
                                {item.label}
                            </Typography>

                            <ValueDisplay value={item.newValue} />
                        </Box>

                        {index < comparison.length - 1 && <Divider />}
                    </Box>
                ))}
            </Box>
        );
    };

    /**
     * VSCode-style comparison.
     */
    const ComparisonView = () => {
        return (
            <Box
                sx={{
                    border: 1,
                    borderColor: "divider",
                    borderRadius: 1,
                    overflow: "hidden",
                }}
            >
                {/* Header */}
                <Box
                    sx={{
                        display: "grid",
                        gridTemplateColumns: {
                            xs: "1fr",
                            sm: "180px 1fr 1fr",
                        },
                        bgcolor: "action.hover",
                    }}
                >
                    <Box
                        sx={{
                            px: 2,
                            py: 1,
                            borderRight: {
                                sm: 1,
                            },
                            borderColor: "divider",
                        }}
                    >
                        <Typography
                            variant="caption"
                            fontWeight={700}
                            color="text.secondary"
                        >
                            PROPERTY
                        </Typography>
                    </Box>

                    <Box
                        sx={{
                            px: 2,
                            py: 1,
                            borderRight: {
                                sm: 1,
                            },
                            borderColor: "divider",
                        }}
                    >
                        <Typography
                            variant="caption"
                            fontWeight={700}
                            color="error.main"
                        >
                            OLD VALUE
                        </Typography>
                    </Box>

                    <Box sx={{ px: 2, py: 1 }}>
                        <Typography
                            variant="caption"
                            fontWeight={700}
                            color="success.main"
                        >
                            NEW VALUE
                        </Typography>
                    </Box>
                </Box>

                {comparison.map((item, index) => {
                    const isChanged = item.type === "changed";
                    const isAdded = item.type === "added";
                    const isRemoved = item.type === "removed";

                    return (
                        <Box key={item.key}>
                            <Box
                                sx={{
                                    display: "grid",
                                    gridTemplateColumns: {
                                        xs: "1fr",
                                        sm: "180px 1fr 1fr",
                                    },
                                    borderTop: 1,
                                    borderColor: "divider",
                                }}
                            >
                                {/* Property */}
                                <Box
                                    sx={{
                                        px: 2,
                                        py: 1.5,
                                        bgcolor: "background.paper",
                                        borderRight: {
                                            sm: 1,
                                        },
                                        borderColor: "divider",
                                    }}
                                >
                                    <Typography
                                        variant="body2"
                                        fontWeight={600}
                                    >
                                        {item.label}
                                    </Typography>

                                    {item.type !== "unchanged" && (
                                        <Chip
                                            size="small"
                                            label={
                                                isChanged
                                                    ? "Changed"
                                                    : isAdded
                                                      ? "Added"
                                                      : "Removed"
                                            }
                                            color={
                                                isChanged
                                                    ? "warning"
                                                    : isAdded
                                                      ? "success"
                                                      : "error"
                                            }
                                            sx={{
                                                mt: 0.75,
                                                height: 20,
                                                fontSize: "0.65rem",
                                            }}
                                        />
                                    )}
                                </Box>

                                {/* Old */}
                                <Box
                                    sx={{
                                        px: 2,
                                        py: 1.5,
                                        borderRight: {
                                            sm: 1,
                                        },
                                        borderColor: "divider",

                                        // Red background only
                                        bgcolor:
                                            isChanged || isRemoved
                                                ? "rgba(211, 47, 47, 0.15)"
                                                : "transparent",

                                        // Red left border
                                        borderLeft:
                                            isChanged || isRemoved
                                                ? "3px solid"
                                                : "3px solid transparent",

                                        borderLeftColor:
                                            isChanged || isRemoved
                                                ? "error.main"
                                                : "transparent",
                                    }}
                                >
                                    <ValueDisplay
                                        value={item.oldValue}
                                        exists={item.oldExists}
                                        changed={isChanged || isRemoved}
                                    />
                                </Box>

                                {/* New */}
                                <Box
                                    sx={{
                                        px: 2,
                                        py: 1.5,

                                        // Green background only
                                        bgcolor:
                                            isChanged || isAdded
                                                ? "rgba(46, 125, 50, 0.15)"
                                                : "transparent",

                                        // Green left border
                                        borderLeft:
                                            isChanged || isAdded
                                                ? "3px solid"
                                                : "3px solid transparent",

                                        borderLeftColor:
                                            isChanged || isAdded
                                                ? "success.main"
                                                : "transparent",
                                    }}
                                >
                                    <ValueDisplay
                                        value={item.newValue}
                                        exists={item.newExists}
                                        changed={isChanged || isAdded}
                                    />
                                </Box>
                            </Box>
                        </Box>
                    );
                })}
            </Box>
        );
    };

    return (
        <>
            <CButton
                sx={{
                    background: (theme) => theme.palette.gradients.primary,
                    boxShadow: (theme) => theme.palette.shadows.primaryButton,
                    transition: "all 0.2s ease",

                    "&:hover": {
                        background: (theme) =>
                            theme.palette.gradients.primaryHover,
                        transform: "translateY(-2px)",
                        boxShadow: (theme) =>
                            theme.palette.shadows.primaryButtonHover,
                    },

                    "&:active": {
                        transform: "translateY(0)",
                        boxShadow: (theme) =>
                            theme.palette.shadows.primaryButtonActive,
                    },

                    "& .MuiButton-startIcon": {
                        transition: "transform 0.2s ease",
                    },

                    "&:hover .MuiButton-startIcon": {
                        transform: "scale(1.15) rotate(-5deg)",
                    },
                    ...sx,
                }}
                onClick={() => setOpen(true)}
            >
                Show Details
            </CButton>

            <CModal
                title="Activity Log Details"
                titleIcon="HistoryIcon"
                width={900}
                open={open}
                onClose={() => setOpen(false)}
            >
                <Box>
                    {/* Summary */}
                    {hasOldProperties ? (
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                gap: 1,
                                mb: 2,
                            }}
                        >
                            <Chip
                                size="small"
                                label="Changes"
                                color="primary"
                            />

                            <Typography variant="body2" color="text.secondary">
                                Showing changes between the old and new values.
                            </Typography>
                        </Box>
                    ) : (
                        <Typography
                            variant="body2"
                            color="text.secondary"
                            sx={{ mb: 2 }}
                        >
                            Activity log properties
                        </Typography>
                    )}

                    {hasOldProperties ? <ComparisonView /> : <PropertyList />}

                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={() => setOpen(false)} />
                    </Box>
                </Box>
            </CModal>
        </>
    );
};

export default ShowActivityLogDetails;
