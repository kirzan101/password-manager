import { CAlertSuccess, CAlertError, CAlertMessage } from "@/Components";

const AlertTransaction = ({ flash }) => {
    return (
        <>
            {flash.success && (
                <CAlertSuccess sx={{ mb: 2 }}>{flash.success}</CAlertSuccess>
            )}
            {flash.error && (
                <CAlertError sx={{ mb: 2 }}>{flash.error}</CAlertError>
            )}
            {flash.message && (
                <CAlertMessage sx={{ mb: 2 }}>{flash.message}</CAlertMessage>
            )}
        </>
    );
};

export default AlertTransaction;
