import VisibilityIcon from "@mui/icons-material/Visibility";
import EditIcon from "@mui/icons-material/Edit";
import ShieldIcon from "@mui/icons-material/Shield";

// Mirrors Helper::CREDENTIAL_ACCESS_LEVELS (rank 1 = highest).
export const ACCESS_LEVELS = [
    {
        value: "viewer",
        label: "Viewer",
        rank: 3,
        power: 33,
        color: "info",
        icon: VisibilityIcon,
        description: "Can view the credential",
    },
    {
        value: "editor",
        label: "Editor",
        rank: 2,
        power: 66,
        color: "warning",
        icon: EditIcon,
        description: "Can view and edit the credential",
    },
    {
        value: "moderator",
        label: "Moderator",
        rank: 1,
        power: 100,
        color: "success",
        icon: ShieldIcon,
        description: "Can manage the credential and its access",
    },
];

export const getAccessLevel = (value) =>
    ACCESS_LEVELS.find((l) => l.value === value);
