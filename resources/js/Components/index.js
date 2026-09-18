// =============================================================================
// BARREL EXPORT FILE
// =============================================================================
//
// Purpose:
// This file acts as a central export hub ("barrel export") for reusable
// components and layouts.
//
// Instead of importing components using long paths like:
//
// import CButton from "@/Components/Customs/Buttons/CButton";
//
// we can simply do:
//
// import { CButton, AppLayout } from "@/Components";
//
// Benefits:
// - Cleaner and shorter imports
// - Easier refactoring (change paths in one place only)
// - Better organization for reusable UI components
// - Similar developer experience to Vue global components,
//   but follows React best practices
// =============================================================================

// Customs Components start

// Alerts
export { default as CAlert } from "./Customs/Alerts/CAlert";
export { default as CAlertError } from "./Customs/Alerts/CAlertError";
export { default as CAlertSuccess } from "./Customs/Alerts/CAlertSuccess";
export { default as CAlertMessage } from "./Customs/Alerts/CAlertMessage";

// Autocompletes
export { default as CAutocomplete } from "./Customs/Autocompletes/CAutocomplete";

// Boxes
export { default as CBox } from "./Customs/Boxes/CBox";
export { default as CBoxContent } from "./Customs/Boxes/CBoxContent";

// Buttons
export { default as CButton } from "./Customs/Buttons/CButton";
export { default as CButtonAdd } from "./Customs/Buttons/CButtonAdd";
export { default as CButtonEdit } from "./Customs/Buttons/CButtonEdit";
export { default as CButtonClose } from "./Customs/Buttons/CButtonClose";
export { default as CButtonSubmit } from "./Customs/Buttons/CButtonSubmit";

// Cards
export { default as CCard } from "./Customs/Cards/CCard";
export { default as CCardContent } from "./Customs/Cards/CCardContent";
export { default as CCardActions } from "./Customs/Cards/CCardActions";

// Chips
export { default as CChip } from "./Customs/Chips/CChip";

// Containers
export { default as CContainer } from "./Customs/Containers/CContainer";

// Data Grids
export { default as CDataGrid } from "./Customs/DataGrids/CDataGrid";

// Fabs
export { default as CFabSubmit } from "./Customs/Fabs/CFabSubmit";

// Grids
export { default as CFormGrid } from "./Customs/Grids/CFormGrid";
export { default as CFormRow } from "./Customs/Grids/CFormRow";

// Icon Buttons
export { default as CIconButton } from "./Customs/IconButtons/CIconButton";

// Modals
export { default as CModal } from "./Customs/Modals/CModal";
export { default as CModalFull } from "./Customs/Modals/CModalFull";

// Selects
export { default as CSelect } from "./Customs/Selects/CSelect";
export { default as CSelectMultiple } from "./Customs/Selects/CSelectMultiple";
export { default as CSelectIcon } from "./Customs/Selects/CSelectIcon";

// Switches
export { default as CSwitch } from "./Customs/Switches/CSwitch";
export { default as CSwitchLabeled } from "./Customs/Switches/CSwitchLabeled";

// TextFields
export { default as CTextField } from "./Customs/TextFields/CTextField";
export { default as CSearchField } from "./Customs/TextFields/CSearchField";
export { default as CPasswordField } from "./Customs/TextFields/CPasswordField";

// Customs Components end

// Layouts
export { default as AppLayout } from "../Layouts/AppLayout";
export { default as EmptyLayout } from "../Layouts/EmptyLayout";

// Utilities
export { default as ThemeButton } from "./Utilities/ThemeButton";
export { default as AvatarUpload } from "./Utilities/AvatarUpload";
