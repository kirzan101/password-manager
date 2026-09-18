import { CDataGrid } from "@/Components";
import { useMemo } from "react";

import EditRole from "../Actions/EditRole";

const TableRole = ({
    flash,
    errors,
    search,
    refreshKey,
    onRefresh,
    permissions,
    moduleLists,
    can,
}) => {
    const columns = useMemo(
        () => [
            {
                field: "name",
                headerName: "Name",
                width: 300,
                renderCell: (params) => {
                    return (
                        <EditRole
                            role={params.row}
                            flash={flash}
                            errors={errors}
                            permissions={permissions}
                            moduleLists={moduleLists}
                            can={can}
                            sx={{
                                minHeight: 28,
                                py: 0,
                                m: 0,
                            }}
                            onSuccess={onRefresh}
                        />
                    );
                },
            },
            {
                field: "is_active",
                headerName: "Is Active",
                width: 150,
                sortable: false,
                renderCell: (params) => (params.row.is_active ? "Yes" : "No"),
            },
            { field: "description", headerName: "Description", flex: 1 },
        ],
        [flash, errors, permissions, moduleLists, can, onRefresh],
    );

    const queryParams = useMemo(
        () => ({
            search,
            refreshKey,
        }),
        [search, refreshKey],
    );

    return (
        <CDataGrid url="/roles" columns={columns} queryParams={queryParams} />
    );
};

export default TableRole;
