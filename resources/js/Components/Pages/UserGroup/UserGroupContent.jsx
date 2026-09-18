import { useEffect, useState, useCallback } from "react";
import { Typography, Grid } from "@mui/material";
import { CBoxContent, CSearchField } from "@/Components";

import AddUserGroup from "./Actions/AddUserGroup";
import AlertTransaction from "@/Components/Utilities/AlertTransaction";
import TableUserGroup from "./Tables/TableUserGroup";

const UserGroupContent = ({ flash, errors, can, userGroupTypes }) => {
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
                    <Typography variant="h4">User Groups</Typography>

                    <AddUserGroup
                        sx={{
                            display: "inline-flex",
                            ml: { xs: "auto", md: 0 },
                        }}
                        flash={flash}
                        errors={errors}
                        can={can}
                        userGroupTypes={userGroupTypes}
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
                        label="Search User Groups"
                        placeholder="Type to search user groups..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </Grid>
            </Grid>

            {/* Display flash messages if they exist */}
            {flash?.error && <AlertTransaction flash={flash} />}

            <TableUserGroup
                flash={flash}
                errors={errors}
                can={can}
                userGroupTypes={userGroupTypes}
                search={debouncedSearch}
                refreshKey={refreshKey}
                onRefresh={refreshTable}
            />
        </CBoxContent>
    );
};

export default UserGroupContent;
