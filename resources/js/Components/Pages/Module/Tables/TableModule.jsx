import { CDataGrid } from "@/Components";
import { useMemo } from "react";

import EditModule from "../Actions/EditModule";

const TableModule = ({
    flash,
    errors,
    search,
    refreshKey,
    onRefresh,
    can,
    categories,
}) => {
    const columns = useMemo(
        () => [
            { field: "id", headerName: "ID", width: 70 },
            {
                field: "name",
                headerName: "Name",
                width: 300,
                renderCell: (params) => {
                    return (
                        <EditModule
                            module={params.row}
                            flash={flash}
                            errors={errors}
                            can={can}
                            categories={categories}
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
            { field: "route", headerName: "Route", width: 200 },
            { field: "category", headerName: "Category", flex: 1 },
        ],
        [flash, errors, can, categories, onRefresh],
    );

    const queryParams = useMemo(
        () => ({
            search,
            refreshKey,
        }),
        [search, refreshKey],
    );

    return (
        <CDataGrid url="/modules" columns={columns} queryParams={queryParams} />
    );
};

export default TableModule;
