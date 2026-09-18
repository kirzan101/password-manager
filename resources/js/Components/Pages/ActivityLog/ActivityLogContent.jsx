import { useEffect, useState, useCallback } from "react";
import { Typography, Grid } from "@mui/material";
import {
    CBoxContent,
    CSearchField,
    CTextField,
    CSelect,
    CAutocomplete,
} from "@/Components";

import AlertTransaction from "@/Components/Utilities/AlertTransaction";
import TableActivityLog from "./Tables/TableActivityLog";

const ActivityLogContent = ({ flash, errors, can, userGroupTypes }) => {
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [refreshKey, setRefreshKey] = useState(0);

    const [filters, setFilters] = useState({
        start_date: "",
        end_date: "",
        type: "",
        module: "",
    });
    const [debouncedFilters, setDebouncedFilters] = useState(filters);

    const handleFilterChange = (newFilters) => {
        setFilters((prev) => ({
            ...prev,
            ...newFilters,
        }));
    };

    const refreshTable = useCallback(() => {
        setRefreshKey((key) => key + 1);
    }, []);

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
        }, 500); // 500ms delay

        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedFilters(filters);
        }, 500); // 500ms delay

        return () => clearTimeout(timer);
    }, [filters]);

    return (
        <CBoxContent>
            <Grid
                container
                spacing={2}
                sx={{ mb: 2, display: "flex", alignItems: "center" }}
            >
                {/* Left: Title + Button */}
                <Grid
                    size={{ xs: 12, md: 6 }}
                    sx={{
                        display: "flex",
                        alignItems: "center",
                        gap: 2,
                    }}
                >
                    <Typography variant="h4">Activity Logs</Typography>
                </Grid>

                {/* Right: Search */}
                <Grid
                    size={{ xs: 12, md: 6 }}
                    display="flex"
                    sx={{
                        justifyContent: {
                            xs: "flex-start",
                            md: "flex-end",
                        },
                    }}
                >
                    <CSearchField
                        sx={{
                            width: {
                                xs: "100%",
                            },
                        }}
                        label="Search Activity Logs"
                        placeholder="Type to search activity logs property..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </Grid>

                {/* Start date filter */}
                <Grid size={{ xs: 6, md: 3 }}>
                    <CTextField
                        label="Process start"
                        value={filters.start_date}
                        type="date"
                        onChange={(e) =>
                            handleFilterChange({
                                start_date: e.target.value,
                            })
                        }
                    />
                </Grid>
                <Grid size={{ xs: 6, md: 3 }}>
                    <CTextField
                        label="Process end"
                        value={filters.end_date}
                        type="date"
                        onChange={(e) =>
                            handleFilterChange({
                                end_date: e.target.value,
                            })
                        }
                    />
                </Grid>
                {/* End date filter */}

                <Grid size={{ xs: 6, md: 3 }}>
                    <CAutocomplete
                        label="Type"
                        name="type_filter"
                        value={filters.type}
                        onChange={(e) =>
                            handleFilterChange({
                                type: e.target.value,
                            })
                        }
                        options={[
                            { value: "create", label: "Create" },
                            { value: "update", label: "Update" },
                            { value: "delete", label: "Delete" },
                            { value: "view", label: "View" },
                            { value: "login", label: "Login" },
                            { value: "logout", label: "Logout" },
                            { value: "export", label: "Export" },
                            { value: "import", label: "Import" },
                            { value: "set-status", label: "Set Status" },
                            { value: "set-avatar", label: "Set Avatar" },
                            { value: "approve", label: "Approve" },
                        ]}
                    />
                </Grid>

                <Grid size={{ xs: 6, md: 3 }}>
                    <CAutocomplete
                        label="Module"
                        name="module_filter"
                        value={filters.module}
                        onChange={(e) =>
                            handleFilterChange({
                                module: e.target.value,
                            })
                        }
                        options={[
                            { value: "auth", label: "Auth" },
                            { value: "profiles", label: "Profile" },
                            { value: "user_groups", label: "User Groups" },
                            { value: "roles", label: "Roles" },
                        ]}
                    />
                </Grid>
            </Grid>

            {/* Display flash messages if they exist */}
            {flash?.error && <AlertTransaction flash={flash} />}

            <TableActivityLog
                flash={flash}
                errors={errors}
                can={can}
                search={debouncedSearch}
                filters={debouncedFilters}
                refreshKey={refreshKey}
                onRefresh={refreshTable}
            />
        </CBoxContent>
    );
};

export default ActivityLogContent;
