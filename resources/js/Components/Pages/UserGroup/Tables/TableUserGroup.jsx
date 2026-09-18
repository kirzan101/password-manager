import { CDataGrid } from "@/Components";
import { useMemo } from "react";

import EditUserGroup from "../Actions/EditUserGroup";

const TableUserGroup = ({
    flash,
    errors,
    search,
    refreshKey,
    onRefresh,
    can,
    userGroupTypes,
}) => {
    const columns = useMemo(
        () => [
            {
                field: "name",
                headerName: "Name",
                width: 300,
                renderCell: (params) => (
                    <EditUserGroup
                        userGroup={params.row}
                        flash={flash}
                        errors={errors}
                        can={can}
                        userGroupTypes={userGroupTypes}
                        sx={{
                            minHeight: 28,
                            py: 0,
                            m: 0,
                        }}
                        onSuccess={onRefresh}
                    />
                ),
            },
            {
                field: "code",
                headerName: "Code",
                width: 150,
            },
            {
                field: "description",
                headerName: "Description",
                width: 150,
            },
        ],
        [flash, errors, can, userGroupTypes, onRefresh],
    );

    const queryParams = useMemo(
        () => ({
            search,
            refreshKey,
        }),
        [search, refreshKey],
    );

    return (
        <CDataGrid
            url="/user-groups"
            columns={columns}
            queryParams={queryParams}
        />
    );
};

export default TableUserGroup;
