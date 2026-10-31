import { useState } from "react";
import { Box, IconButton, Tooltip } from "@mui/material";
import EditOutlinedIcon from "@mui/icons-material/EditOutlined";
import { router } from "@inertiajs/react";
import { CModal, CButtonClose, CButtonSubmit } from "@/Components";

import FormCredential from "./Forms/FormCredential";
import { fieldsToProperties, propertiesToFields } from "../categoryDefaults";

const EditCredential = ({ credential, errors = {}, can = [], onSuccess }) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);
    const [form, setForm] = useState({});
    const [fields, setFields] = useState([]);

    const canUpdate = can.includes("update-credentials");
    if (!canUpdate) return null;

    const handleOpen = () => {
        setForm({
            name: credential.name ?? "",
            username: credential.username ?? "",
            secret: "", // blank keeps the current secret
            notes: credential.notes ?? "",
            category: credential.category ?? "",
        });
        setFields(propertiesToFields(credential.properties));
        setOpen(true);
    };

    const handleClose = () => setOpen(false);

    const handleSubmit = (event) => {
        event.preventDefault();

        router.post(
            `/credentials/${credential.id}`,
            {
                _method: "PUT",
                ...form,
                properties: fieldsToProperties(fields),
            },
            {
                onSuccess: () => {
                    handleClose();
                    onSuccess?.();
                },
                onBefore: () => setBtnDisabled(true),
                onFinish: () => setBtnDisabled(false),
            },
        );
    };

    return (
        <>
            <Tooltip title="Edit">
                <IconButton size="small" onClick={handleOpen}>
                    <EditOutlinedIcon fontSize="small" />
                </IconButton>
            </Tooltip>

            <CModal
                title={`Editing ${credential.name}`}
                titleIcon="EditIcon"
                width={600}
                open={open}
                onClose={handleClose}
            >
                <form onSubmit={handleSubmit}>
                    <FormCredential
                        form={form}
                        setForm={setForm}
                        fields={fields}
                        setFields={setFields}
                        errors={errors}
                        secretPlaceholder="Leave blank to keep current secret"
                    />
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "flex-end",
                            mt: 2,
                        }}
                    >
                        <CButtonClose onClick={handleClose} />
                        <CButtonSubmit
                            sx={{ ml: 1, mr: 0 }}
                            loading={btnDisabled}
                        />
                    </Box>
                </form>
            </CModal>
        </>
    );
};

export default EditCredential;
