import { useEffect, useState, useCallback } from "react";
import { Typography, Grid } from "@mui/material";
import { CBoxContent, CSearchField } from "@/Components";

import AddRole from "./Actions/AddRole";
import AlertTransaction from "@/Components/Utilities/AlertTransaction";
import TableRole from "./Tables/TableRole";

const RoleContent = ({ flash, errors, permissions, moduleLists, can }) => {
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [refreshKey, setRefreshKey] = useState(0);

    const refreshTable = useCallback(() => {
        setRefreshKey((key) => key + 1);
    }, []);

    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
        }, 500); // 500ms delay

        return () => clearTimeout(timer);
    }, [search]);

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
                    <Typography variant="h4">Roles</Typography>

                    <AddRole
                        sx={{
                            display: "inline-flex",
                            ml: { xs: "auto", md: 0 },
                        }}
                        flash={flash}
                        errors={errors}
                        permissions={permissions}
                        moduleLists={moduleLists}
                        can={can}
                        onSuccess={refreshTable}
                    />
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
                        label="Search Roles"
                        placeholder="Type to search roles..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </Grid>
            </Grid>

            {/* Display flash messages if they exist */}
            {flash?.error && <AlertTransaction flash={flash} />}

            <TableRole
                flash={flash}
                errors={errors}
                can={can}
                permissions={permissions}
                moduleLists={moduleLists}
                refreshKey={refreshKey}
                search={debouncedSearch}
                onRefresh={refreshTable}
            />
        </CBoxContent>
    );
};

export default RoleContent;
