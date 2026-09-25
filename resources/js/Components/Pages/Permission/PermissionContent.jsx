import { useEffect, useState, useCallback } from "react";
import { Grid, Box, Typography, CircularProgress, Grow } from "@mui/material";
import ModuleCard from "./Cards/ModuleCard";
import EmptyResult from "@/Components/Utilities/EmptyResult";

import { CBoxContent, CSearchField } from "@/Components";

import AlertTransaction from "@/Components/Utilities/AlertTransaction";
const PermissionContent = ({ flash, errors, can, permissionsByModule }) => {
    const [search, setSearch] = useState("");
    const [isSearching, setIsSearching] = useState(false);

    useEffect(() => {
        setIsSearching(true);

        const timer = setTimeout(() => {
            setIsSearching(false);
        }, 300);

        return () => clearTimeout(timer);
    }, [search]);

    // implement search functionality for modules based on the search state
    const filteredPermissionsByModule = Object.fromEntries(
        Object.entries(permissionsByModule).filter(([moduleName]) =>
            moduleName.toLowerCase().includes(search.toLowerCase()),
        ),
    );

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
                    <Typography variant="h4">Permissions</Typography>
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
                        label="Search module"
                        placeholder="Type to search module..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </Grid>
            </Grid>

            {/* Display flash messages if they exist */}
            {flash?.error && <AlertTransaction flash={flash} />}

            <Grid container spacing={3}>
                {isSearching ? (
                    <Grid
                        size={12}
                        sx={{
                            minHeight: 240,
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                        }}
                    >
                        <CircularProgress
                            size={32}
                            thickness={4}
                            color="primary"
                        />
                    </Grid>
                ) : Object.keys(filteredPermissionsByModule).length > 0 ? (
                    Object.entries(filteredPermissionsByModule).map(
                        ([moduleName, permissions], index) => (
                            <Grid
                                key={moduleName}
                                size={{
                                    xs: 12,
                                    sm: 6,
                                    md: 6,
                                    lg: 4,
                                    xl: 3,
                                    xxl: 2,
                                }}
                            >
                                {/* <ModuleCard
                                    moduleName={moduleName}
                                    permissions={permissions}
                                    flash={flash}
                                    errors={errors}
                                    can={can}
                                /> */}

                                <Grow
                                    in
                                    timeout={300 + index * 75}
                                    style={{
                                        transformOrigin: "0 0 0",
                                    }}
                                >
                                    <div>
                                        <ModuleCard
                                            moduleName={moduleName}
                                            permissions={permissions}
                                            flash={flash}
                                            errors={errors}
                                            can={can}
                                        />
                                    </div>
                                </Grow>
                            </Grid>
                        ),
                    )
                ) : (
                    <Grid
                        size={12}
                        sx={{
                            minHeight: 240,
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                        }}
                    >
                        <EmptyResult message="No modules found." />
                    </Grid>
                )}
            </Grid>
        </CBoxContent>
    );
};
export default PermissionContent;
