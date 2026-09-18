import { useEffect, useState } from "react";
import { CModal, CButtonEdit, CButtonClose, CButtonSubmit } from "@/Components";

import EditLabel from "@/Components/Utilities/EditLabel";
import FormModule from "./Forms/FormModule";
import { Box } from "@mui/material";
import { router } from "@inertiajs/react";

const EditModule = ({
    module,
    flash,
    errors,
    can,
    categories,
    sx,
    onSuccess,
}) => {
    const [open, setOpen] = useState(false);
    const [btnDisabled, setBtnDisabled] = useState(false);

    const buildEmptyForm = () => ({
        id: null,
        name: "",
        icon: "",
        route: "",
        category: "",
        order: "",
        is_active: true,
    });

    const buildFormFromModule = (module) => ({
        id: module?.id ?? null,
        name: module?.name ?? "",
        icon: module?.icon ?? "",
        route: module?.route ?? "",
        category: module?.category ?? "",
        order: module?.order ?? 0,
        is_active: module?.is_active ?? true,
    });

    const [form, setForm] = useState(buildEmptyForm(module));

    // Store the original form value for logging purposes
    const [oldForm, setOldForm] = useState(null);

    // update form value when module props change
    useEffect(() => {
        setForm(buildFormFromModule(module));
    }, [module]);

    const handleOpen = () => {
        const initialForm = buildFormFromModule(module);

        setForm(initialForm);
        setOldForm({ ...initialForm });
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
        setForm(buildEmptyForm(null));
        setOldForm(null);
    };

    // reset form value to initial state
    const handleResetForm = () => {
        if (module) {
            setForm(buildFormFromModule(module));
        } else {
            setForm(buildEmptyForm());
        }
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        // submission here
        router.post(
            `/modules/${module.id}`,
            {
                _method: "PUT",
                forceFormData: true,
                ...form,
                old_properties: oldForm, // add old_properties to the form data for logging purposes
            },
            {
                onSuccess: ({ props }) => {
                    handleClose();

                    // call onSuccess callback if provided
                    onSuccess?.();
                },
                onError: () => {
                    // emits("notification", "Some fields has an error.", "error");
                    // add snackbar here
                },
                onBefore: () => {
                    setBtnDisabled(true);
                },
                onFinish: () => {
                    setBtnDisabled(false);
                },
            },
        );
    };

    const canUpdateModule = can.includes("update-modules");

    return (
        <>
            {canUpdateModule ? (
                <CButtonEdit sx={sx} onClick={handleOpen}>
                    {module.name}
                </CButtonEdit>
            ) : (
                <EditLabel label={module.name} />
            )}

            <CModal
                title={`Editing ${module.name}`}
                width={450}
                open={open}
                onClose={handleClose}
            >
                <form onSubmit={handleSubmit}>
                    <FormModule
                        form={form}
                        setForm={setForm}
                        errors={errors}
                        categories={categories}
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

export default EditModule;
