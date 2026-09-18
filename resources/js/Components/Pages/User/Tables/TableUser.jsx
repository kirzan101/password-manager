import { useMemo } from "react";
import { CDataGrid, CChip } from "@/Components";
import { Box, Stack } from "@mui/material";
import { router } from "@inertiajs/react";

import EditUser from "../Actions/EditUser";
import ResetPassword from "../Actions/ResetPassword";
import SetAccountStatus from "../Actions/SetAccountStatus";
import AvatarUpload from "@/Components/Utilities/AvatarUpload";

const TableUser = ({
    flash,
    errors,
    search,
    refreshKey,
    onRefresh,
    userGroups,
    accountTypes,
    roles,
    can,
}) => {
    const canSetAvatar = can.includes("set-avatar-users");

    const handleAvatarChange = (profileId, blob) => {
        router.post(
            `/change-avatar/${profileId}`,
            {
                _method: "PUT",
                avatar: blob,
            },
            {
                forceFormData: true,
                onSuccess: () => {
                    // refresh the table or component to reflect the new avatar
                    onRefresh();
                },
                onError: (errors) => {
                    console.error("Error changing avatar", errors);
                },
            },
        );
    };

    const columns = useMemo(
        () => [
            {
                field: "name",
                headerName: "Name",
                width: 330,
                renderCell: (params) => {
                    return (
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                gap: 1,
                                width: "100%",
                                minWidth: 0,
                            }}
                        >
                            <AvatarUpload
                                avatarUrl={params.row.avatar}
                                initials={params.row.initials}
                                size={32}
                                onChange={handleAvatarChange.bind(
                                    null,
                                    params.row.id,
                                )}
                                disabled={!canSetAvatar}
                                profileId={params.row.id}
                                onSuccess={onRefresh}
                            />
                            <Box sx={{ flex: 1, minWidth: 0 }}>
                                <EditUser
                                    user={params.row}
                                    flash={flash}
                                    errors={errors}
                                    userGroups={userGroups}
                                    accountTypes={accountTypes}
                                    roles={roles}
                                    can={can}
                                    sx={{
                                        minHeight: 28,
                                        py: 0,
                                        m: 0,
                                    }}
                                    onSuccess={onRefresh}
                                />
                            </Box>
                        </Box>
                    );
                },
            },
            { field: "username", headerName: "Username", width: 200 },
            { field: "email", headerName: "Email", width: 250 },
            // { field: "position", headerName: "Position", width: 200 },
            {
                field: "role_names",
                headerName: "Roles",
                width: 200,
                sortable: false,
                disableColumnMenu: true,
                renderCell: (params) => {
                    return (
                        <Stack
                            direction="row"
                            spacing={1}
                            sx={{
                                width: "100%",
                                height: "100%",
                                alignItems: "center",
                                flexWrap: "wrap",
                                gap: 1,
                            }}
                        >
                            {Object.values(params.row.role_names).map(
                                (roleName, index) => (
                                    <CChip
                                        key={index}
                                        label={roleName}
                                        size="small"
                                        color="primary"
                                        sx={{
                                            minHeight: 28,
                                            py: 0,
                                            m: 0,
                                        }}
                                    />
                                ),
                            )}
                        </Stack>
                    );
                },
            },
            { field: "user_group_name", headerName: "User Group", width: 200 },
            {
                field: "status",
                headerName: "Status",
                width: 100,
                sortable: false,
                disableColumnMenu: true,
                renderCell: (params) => {
                    return (
                        <CChip
                            label={params.row.status}
                            size="small"
                            color={
                                params.row.status === "active"
                                    ? "accent"
                                    : "error"
                            }
                            sx={{ minHeight: 28, py: 0, m: 0 }}
                        />
                    );
                },
            },
            {
                field: "last_login_at",
                headerName: "Last Login At",
                width: 200,
                renderCell: (params) => params.value || "—",
            },
            {
                field: "actions",
                headerName: "Actions",
                width: 150,
                sortable: false,
                disableColumnMenu: true,
                renderCell: (params) => {
                    return (
                        <>
                            <ResetPassword
                                user={params.row}
                                flash={flash}
                                errors={errors}
                                can={can}
                                sx={{ minHeight: 28, py: 0, m: 0 }}
                                onSuccess={onRefresh}
                            />
                            <SetAccountStatus
                                user={params.row}
                                flash={flash}
                                errors={errors}
                                can={can}
                                sx={{ minHeight: 28, py: 0, m: 0 }}
                                onSuccess={onRefresh}
                            />
                        </>
                    );
                },
            },
        ],
        [flash, errors, userGroups, accountTypes, roles, can, onRefresh],
    );

    const queryParams = useMemo(
        () => ({
            search,
            refreshKey,
        }),
        [search, refreshKey],
    );

    return (
        <CDataGrid url="/users" columns={columns} queryParams={queryParams} />
    );
};

export default TableUser;
