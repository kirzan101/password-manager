import { useState, useCallback, useEffect } from "react";
import axios from "@/Utilities/axios";
import { Grid } from "@mui/material";
import EmptyResult from "@/Components/Utilities/EmptyResult";
import CredentialCard from "../Cards/CredentialCard";

const CardList = ({
    flash,
    errors,
    can,
    queryParams = {},
    initialPageSize = 10,
    onSuccess,
}) => {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(false);
    const [rowCount, setRowCount] = useState(0);
    const [sortModel, setSortModel] = useState([{ field: "id", sort: "desc" }]);

    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: initialPageSize,
    });

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

            // showNotification(
            //     "Unable to load credentials. Please try again.",
            //     "error",
            // );
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
            // showNotification(`${fieldName} is empty.`, "warning");
            return;
        }

        try {
            await navigator.clipboard.writeText(value);

            // showNotification(`${fieldName} copied to clipboard.`);
        } catch (error) {
            console.error("Clipboard copy error:", error);

            // showNotification(
            //     "Unable to access the clipboard. Check your browser permissions.",
            //     "error",
            // );
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

            // showNotification("Secret copied to clipboard.");
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

            // showNotification(message, "error");
        } finally {
            setCopyingSecretId(null);
        }
    };

    if (!loading && rows.length === 0) {
        return <EmptyResult />;
    }

    return (
        <Grid container spacing={2}>
            {rows.map((credential) => (
                <Grid key={credential.id} size={{ xs: 12, sm: 6, lg: 4 }}>
                    <CredentialCard
                        credential={credential}
                        onCopy={copyToClipboard}
                        onCopySecret={copySecret}
                        copying={copyingSecretId === credential.id}
                        can={can}
                        errors={errors}
                        onSuccess={fetchData}
                    />
                </Grid>
            ))}
        </Grid>
    );
};

export default CardList;
