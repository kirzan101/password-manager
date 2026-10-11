import { useCallback, useEffect, useState } from "react";

import axios from "@/Utilities/axios";

import {
    Alert,
    Box,
    Button,
    Card,
    CardContent,
    CircularProgress,
    Grid,
    IconButton,
    MenuItem,
    Pagination,
    Select,
    Skeleton,
    Snackbar,
    Stack,
    Tooltip,
    Typography,
} from "@mui/material";

import ContentCopyIcon from "@mui/icons-material/ContentCopy";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import KeyOutlinedIcon from "@mui/icons-material/KeyOutlined";

const CardList = ({
    flash,
    errors,
    can,
    queryParams = {},
    initialPageSize = 10,
}) => {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(false);
    const [rowCount, setRowCount] = useState(0);
    const [sortModel, setSortModel] = useState([{ field: "id", sort: "desc" }]);

    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: initialPageSize,
    });

    const [notification, setNotification] = useState({
        open: false,
        message: "",
        severity: "success",
    });

    const showNotification = (message, severity = "success") => {
        setNotification({
            open: true,
            message,
            severity,
        });
    };

    const closeNotification = (_, reason) => {
        if (reason === "clickaway") return;

        setNotification((prev) => ({
            ...prev,
            open: false,
        }));
    };

    const fetchData = useCallback(async () => {
        setLoading(true);

        try {
            const response = await axios.get("credentials", {
                params: {
                    page: paginationModel.page + 1,
                    per_page: paginationModel.pageSize,
                    sort_by: sortModel[0]?.field,
                    sort: sortModel[0]?.sort,
                    ...queryParams,
                },
            });

            setRows(response.data.data || []);
            setRowCount(response.data.total || 0);
        } catch (error) {
            console.error("Credential list fetch error:", error);

            showNotification(
                "Unable to load credentials. Please try again.",
                "error",
            );
        } finally {
            setLoading(false);
        }
    }, [
        paginationModel.page,
        paginationModel.pageSize,
        JSON.stringify(sortModel),
        JSON.stringify(queryParams),
    ]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    /**
     * Copy a regular field directly to the clipboard.
     */
    const copyToClipboard = async (value, fieldName) => {
        if (typeof value !== "string" || !value.trim()) {
            showNotification(`${fieldName} is empty.`, "warning");
            return;
        }

        try {
            await navigator.clipboard.writeText(value);

            showNotification(`${fieldName} copied to clipboard.`);
        } catch (error) {
            console.error("Clipboard copy error:", error);

            showNotification(
                "Unable to access the clipboard. Check your browser permissions.",
                "error",
            );
        }
    };

    /**
     * Fetch the actual secret only when the user clicks Copy.
     * The Laravel endpoint must authorize access before returning it.
     */
    const [copyingSecretId, setCopyingSecretId] = useState(null);

    const copySecret = async (credentialId) => {
        if (copyingSecretId !== null) return;

        setCopyingSecretId(credentialId);

        try {
            const response = await axios.post(
                `credentials/${credentialId}/reveal`,
            );

            const secret = response.data.secret;

            if (typeof secret !== "string") {
                throw new Error("Invalid secret response.");
            }

            await navigator.clipboard.writeText(secret);

            showNotification("Secret copied to clipboard.");
        } catch (error) {
            console.error("Credential reveal failed.");

            const status = error.response?.status;

            const message =
                status === 401
                    ? "Please sign in again."
                    : status === 403
                      ? "You are not authorized to access this secret."
                      : status === 404
                        ? "Credential not found."
                        : status === 429
                          ? "Too many requests. Please try again later."
                          : "Unable to copy the secret. Please try again.";

            showNotification(message, "error");
        } finally {
            setCopyingSecretId(null);
        }
    };

    /**
     * Reusable field with a copy button.
     */
    const CopyField = ({
        label,
        value,
        onCopy,
        multiline = false,
        dummy = false,
        loading: fieldLoading = false,
    }) => (
        <Box sx={{ minWidth: 0 }}>
            <Typography
                variant="caption"
                color="text.secondary"
                sx={{ display: "block", mb: 0.5 }}
            >
                {label}
            </Typography>

            <Stack
                direction="row"
                spacing={1}
                alignItems={multiline ? "flex-start" : "center"}
            >
                <Typography
                    variant="body2"
                    sx={{
                        flex: 1,
                        minWidth: 0,
                        overflowWrap: "anywhere",
                        whiteSpace: multiline ? "pre-wrap" : "normal",
                        color: dummy ? "text.secondary" : "text.primary",
                        fontStyle: dummy ? "italic" : "normal",
                        minHeight: multiline ? 40 : "auto",
                    }}
                >
                    {value || "—"}
                </Typography>

                <Tooltip title={`Copy ${label}`}>
                    <span>
                        <IconButton
                            size="small"
                            aria-label={`Copy ${label}`}
                            onClick={onCopy}
                            disabled={fieldLoading || !value}
                        >
                            {fieldLoading ? (
                                <CircularProgress size={18} />
                            ) : (
                                <ContentCopyIcon fontSize="small" />
                            )}
                        </IconButton>
                    </span>
                </Tooltip>
            </Stack>
        </Box>
    );

    const totalPages = Math.ceil(rowCount / paginationModel.pageSize);

    return (
        <Box>
            {/* Toolbar */}
            <Stack
                direction={{ xs: "column", sm: "row" }}
                justifyContent="space-between"
                alignItems={{ xs: "stretch", sm: "center" }}
                spacing={2}
                sx={{ mb: 2 }}
            >
                <Typography variant="body2" color="text.secondary">
                    {rowCount} credential{rowCount === 1 ? "" : "s"}
                </Typography>

                <Stack direction="row" alignItems="center" spacing={1}>
                    <Typography variant="body2" color="text.secondary">
                        Sort by
                    </Typography>

                    <Select
                        size="small"
                        value={`${sortModel[0]?.field}:${sortModel[0]?.sort}`}
                        onChange={(event) => {
                            const [field, sort] = event.target.value.split(":");

                            setSortModel([{ field, sort }]);
                            setPaginationModel((prev) => ({
                                ...prev,
                                page: 0,
                            }));
                        }}
                        sx={{ minWidth: 160 }}
                    >
                        <MenuItem value="id:desc">Newest first</MenuItem>
                        <MenuItem value="id:asc">Oldest first</MenuItem>
                        <MenuItem value="name:asc">Name A–Z</MenuItem>
                        <MenuItem value="name:desc">Name Z–A</MenuItem>
                    </Select>
                </Stack>
            </Stack>

            {/* Loading skeletons */}
            {loading && rows.length === 0 ? (
                <Grid container spacing={2}>
                    {Array.from({ length: 3 }).map((_, index) => (
                        <Grid key={index} size={{ xs: 12, sm: 6, md: 4 }}>
                            <Card variant="outlined">
                                <CardContent>
                                    <Skeleton width="60%" height={32} />
                                    <Skeleton sx={{ mt: 2 }} />
                                    <Skeleton />
                                    <Skeleton sx={{ mt: 2 }} />
                                    <Skeleton />
                                </CardContent>
                            </Card>
                        </Grid>
                    ))}
                </Grid>
            ) : rows.length === 0 ? (
                <Card variant="outlined">
                    <CardContent>
                        <Stack alignItems="center" spacing={1} sx={{ py: 5 }}>
                            <KeyOutlinedIcon
                                sx={{ fontSize: 44, color: "text.secondary" }}
                            />

                            <Typography variant="h6">
                                No credentials found
                            </Typography>

                            <Typography
                                variant="body2"
                                color="text.secondary"
                                textAlign="center"
                            >
                                Your saved credentials will appear here.
                            </Typography>
                        </Stack>
                    </CardContent>
                </Card>
            ) : (
                <>
                    <Grid container spacing={2}>
                        {rows.map((credential) => {
                            const notes =
                                credential.notes ??
                                credential.properties?.notes ??
                                "";

                            return (
                                <Grid
                                    key={credential.id}
                                    size={{ xs: 12, sm: 6, md: 4 }}
                                >
                                    <Card
                                        variant="outlined"
                                        sx={{
                                            height: "100%",
                                            display: "flex",
                                            flexDirection: "column",
                                            transition: "box-shadow 0.2s",
                                            "&:hover": {
                                                boxShadow: 3,
                                            },
                                        }}
                                    >
                                        <CardContent
                                            sx={{
                                                display: "flex",
                                                flexDirection: "column",
                                                gap: 2.5,
                                                flex: 1,
                                            }}
                                        >
                                            {/* Card heading */}
                                            <Stack
                                                direction="row"
                                                spacing={1}
                                                alignItems="center"
                                            >
                                                <Box
                                                    sx={{
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent:
                                                            "center",
                                                        width: 40,
                                                        height: 40,
                                                        borderRadius: 2,
                                                        bgcolor: "action.hover",
                                                    }}
                                                >
                                                    <KeyOutlinedIcon color="action" />
                                                </Box>

                                                <Box
                                                    sx={{
                                                        minWidth: 0,
                                                        flex: 1,
                                                    }}
                                                >
                                                    <Typography
                                                        variant="h6"
                                                        sx={{
                                                            fontSize: "1rem",
                                                            overflowWrap:
                                                                "anywhere",
                                                        }}
                                                    >
                                                        {credential.name ||
                                                            "Untitled credential"}
                                                    </Typography>

                                                    <Typography
                                                        variant="caption"
                                                        color="text.secondary"
                                                    >
                                                        ID: {credential.id}
                                                    </Typography>
                                                </Box>
                                            </Stack>

                                            {/* Name */}
                                            <CopyField
                                                label="Name"
                                                value={credential.name || ""}
                                                onCopy={() =>
                                                    copyToClipboard(
                                                        credential.name,
                                                        "Name",
                                                    )
                                                }
                                            />

                                            {/* Username */}
                                            <CopyField
                                                label="Username"
                                                value={
                                                    credential.username || ""
                                                }
                                                onCopy={() =>
                                                    copyToClipboard(
                                                        credential.username,
                                                        "Username",
                                                    )
                                                }
                                            />

                                            {/* Notes */}
                                            <CopyField
                                                label="Notes"
                                                value={notes}
                                                multiline
                                                onCopy={() =>
                                                    copyToClipboard(
                                                        notes,
                                                        "Notes",
                                                    )
                                                }
                                            />

                                            {/* Secret: dummy value only */}
                                            <Box
                                                sx={{
                                                    p: 1.5,
                                                    borderRadius: 1,
                                                    bgcolor: "action.hover",
                                                }}
                                            >
                                                <Stack
                                                    direction="row"
                                                    spacing={1}
                                                    alignItems="center"
                                                    sx={{ mb: 1 }}
                                                >
                                                    <LockOutlinedIcon
                                                        fontSize="small"
                                                        color="action"
                                                    />

                                                    <Typography
                                                        variant="caption"
                                                        color="text.secondary"
                                                    >
                                                        Secret
                                                    </Typography>
                                                </Stack>

                                                <CopyField
                                                    label="Secret"
                                                    value="••••••••••••"
                                                    dummy
                                                    onCopy={() =>
                                                        copySecret(
                                                            credential.id,
                                                        )
                                                    }
                                                    fieldLoading={
                                                        copyingSecretId ===
                                                        credential.id
                                                    }
                                                />
                                            </Box>
                                        </CardContent>
                                    </Card>
                                </Grid>
                            );
                        })}
                    </Grid>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <Stack
                            direction={{ xs: "column", sm: "row" }}
                            justifyContent="space-between"
                            alignItems="center"
                            spacing={2}
                            sx={{ mt: 3 }}
                        >
                            <Select
                                size="small"
                                value={paginationModel.pageSize}
                                onChange={(event) => {
                                    setPaginationModel({
                                        page: 0,
                                        pageSize: Number(event.target.value),
                                    });
                                }}
                            >
                                {[10, 20, 50, 100].map((size) => (
                                    <MenuItem key={size} value={size}>
                                        {size} per page
                                    </MenuItem>
                                ))}
                            </Select>

                            <Pagination
                                color="primary"
                                page={paginationModel.page + 1}
                                count={totalPages}
                                onChange={(_, page) => {
                                    setPaginationModel((prev) => ({
                                        ...prev,
                                        page: page - 1,
                                    }));
                                }}
                            />
                        </Stack>
                    )}

                    {loading && (
                        <Stack
                            direction="row"
                            alignItems="center"
                            spacing={1}
                            sx={{ mt: 2 }}
                        >
                            <CircularProgress size={18} />
                            <Typography variant="body2" color="text.secondary">
                                Updating credentials...
                            </Typography>
                        </Stack>
                    )}
                </>
            )}

            {/* Notifications */}
            <Snackbar
                open={notification.open}
                autoHideDuration={3000}
                onClose={closeNotification}
                anchorOrigin={{
                    vertical: "bottom",
                    horizontal: "right",
                }}
            >
                <Alert
                    severity={notification.severity}
                    variant="filled"
                    onClose={closeNotification}
                    sx={{ width: "100%" }}
                >
                    {notification.message}
                </Alert>
            </Snackbar>
        </Box>
    );
};

export default CardList;
