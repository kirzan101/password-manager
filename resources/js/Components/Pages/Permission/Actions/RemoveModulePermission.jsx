import { useEffect, useState } from "react";
import {
    CModal,
    CButtonClose,
    CButtonSubmit,
    CIconButton,
    CButton,
} from "@/Components";
import { Box, Typography } from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";

import { router } from "@inertiajs/react";

const RemoveModulePermission = ({ permission, onSuccess, can, errors }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const canDelete = can.includes("delete-permissions");
    const handleDelete = (event) => {
        event.preventDefault();

        if (!canDelete) {
            return;
        }

        router.delete(`/permissions/${permission.permission_id}`, {
            onSuccess: () => {
                setOpen(false);
                onSuccess?.();
            },
            onError: () => {
                // handle error
            },
            onBefore: () => {
                setBtnDisabled(true);
            },
            onFinish: () => {
                setBtnDisabled(false);
            },
        });
    };

    return (
        <>
            {canDelete && (
                <CIconButton
                    icon="DeleteIcon"
                    color="error"
                    size="small"
                    tooltip="Remove"
                    sx={{ m: 0 }}
                    onClick={() => setOpen(true)}
                />
            )}

            <CModal
                title={`Remove "${permission?.type}" Permission`}
                titleIcon="DeleteIcon"
                width={450}
                open={open}
                onClose={() => setOpen(false)}
            >
                <Box>
                    <Typography>
                        Are you sure you want to <b>REMOVE</b> this permission?
                    </Typography>
                    <Box
                        sx={{
                            mt: 2,
                            display: "flex",
                            justifyContent: "flex-end",
                        }}
                    >
                        <CButtonClose
                            onClick={() => setOpen(false)}
                            sx={{ mr: 1 }}
                        />
                        <CButton
                            sx={{ ml: 1, mr: 0 }}
                            startIcon={<DeleteIcon />}
                            onClick={handleDelete}
                            disabled={btnDisabled}
                            color="error"
                            type="submit"
                        >
                            Remove
                        </CButton>
                    </Box>
                </Box>
            </CModal>
        </>
    );
};

export default RemoveModulePermission;
