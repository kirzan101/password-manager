import { useState } from "react";
import {
    Avatar,
    Box,
    Button,
    Chip,
    IconButton,
    LinearProgress,
    MenuItem,
    Stack,
    TextField,
    Tooltip,
    Typography,
} from "@mui/material";
import ShareOutlinedIcon from "@mui/icons-material/ShareOutlined";
import GroupsIcon from "@mui/icons-material/Groups";
import PersonIcon from "@mui/icons-material/Person";
import DeleteOutlinedIcon from "@mui/icons-material/DeleteOutlined";
import { CModal, CButtonClose } from "@/Components";
import { ACCESS_LEVELS, getAccessLevel } from "./accessLevels";

/**
 * UI-only share dialog for a credential (no backend calls).
 *
 * Props:
 * - credential: { id, name }
 * - accesses: [{ id, profile_id?, user_group_id?, name, access_level }]
 * - profiles / userGroups: [{ id, name }] selectable targets
 * - canShare: boolean
 * - onShare({ credential_id, target_type: "profile"|"user_group",
 *             profile_id?, user_group_id?, access_level })
 * - onChangeLevel(access, access_level)
 * - onRevoke(access)
 */
const ShareCredential = ({
    credential,
    accesses = [],
    profiles = [],
    userGroups = [],
    canShare = true,
    onShare,
    onChangeLevel,
    onRevoke,
}) => {
    const [open, setOpen] = useState(false);
    const [targetType, setTargetType] = useState("user_group");
    const [targetId, setTargetId] = useState("");
    const [level, setLevel] = useState("viewer");
    const [error, setError] = useState("");

    const targets = targetType === "profile" ? profiles : userGroups;

    const handleClose = () => {
        setOpen(false);
        setTargetId("");
        setLevel("viewer");
        setError("");
    };

    const handleShare = () => {
        if (!targetId) {
            setError("Select who to share with");
            return;
        }

        onShare?.({
            credential_id: credential.id,
            target_type: targetType,
            profile_id: targetType === "profile" ? targetId : null,
            user_group_id: targetType === "user_group" ? targetId : null,
            access_level: level,
        });
        setTargetId("");
        setError("");
    };

    const selected = getAccessLevel(level);

    return (
        <>
            <Tooltip title={canShare ? "Share" : "You can't share this"}>
                <span>
                    <IconButton
                        size="small"
                        disabled={!canShare}
                        onClick={() => setOpen(true)}
                    >
                        <ShareOutlinedIcon fontSize="small" />
                    </IconButton>
                </span>
            </Tooltip>

            <CModal
                title={`Share ${credential?.name ?? ""}`}
                titleIcon="ShareIcon"
                width={560}
                open={open}
                onClose={handleClose}
            >
                {/* Level picker */}
                <Typography variant="subtitle2" fontWeight={700} sx={{ mb: 1 }}>
                    Choose access level
                </Typography>
                <Stack direction="row" spacing={1} sx={{ mb: 1 }}>
                    {ACCESS_LEVELS.map((l) => {
                        const Icon = l.icon;
                        const active = l.value === level;
                        return (
                            <Box
                                key={l.value}
                                onClick={() => setLevel(l.value)}
                                sx={{
                                    flex: 1,
                                    p: 1.5,
                                    cursor: "pointer",
                                    textAlign: "center",
                                    borderRadius: 3,
                                    border: "2px solid",
                                    borderColor: active
                                        ? `${l.color}.main`
                                        : "divider",
                                    backgroundColor: active
                                        ? "action.hover"
                                        : "transparent",
                                    transition: "all 0.2s ease",
                                    "&:hover": {
                                        transform: "translateY(-2px)",
                                    },
                                }}
                            >
                                <Icon color={active ? l.color : "disabled"} />
                                <Typography variant="body2" fontWeight={700}>
                                    {l.label}
                                </Typography>
                            </Box>
                        );
                    })}
                </Stack>
                <Typography variant="caption" color="text.secondary">
                    {selected.description}
                </Typography>
                <LinearProgress
                    variant="determinate"
                    value={selected.power}
                    color={selected.color}
                    sx={{ mt: 0.5, mb: 2, height: 8, borderRadius: 4 }}
                />

                {/* Target picker */}
                <Stack direction="row" spacing={1} sx={{ mb: 1.5 }}>
                    <Chip
                        icon={<GroupsIcon />}
                        label="Group"
                        color={
                            targetType === "user_group" ? "primary" : "default"
                        }
                        onClick={() => {
                            setTargetType("user_group");
                            setTargetId("");
                        }}
                    />
                    <Chip
                        icon={<PersonIcon />}
                        label="Person"
                        color={targetType === "profile" ? "primary" : "default"}
                        onClick={() => {
                            setTargetType("profile");
                            setTargetId("");
                        }}
                    />
                </Stack>

                <Stack
                    direction="row"
                    spacing={1}
                    sx={{ alignItems: "flex-start" }}
                >
                    <TextField
                        select
                        fullWidth
                        size="small"
                        label={
                            targetType === "profile" ? "Person" : "User group"
                        }
                        value={targetId}
                        onChange={(e) => {
                            setTargetId(e.target.value);
                            setError("");
                        }}
                        error={!!error}
                        helperText={error}
                    >
                        {targets.map((t) => (
                            <MenuItem key={t.id} value={t.id}>
                                {t.name}
                            </MenuItem>
                        ))}
                    </TextField>
                    <Button variant="contained" onClick={handleShare}>
                        Share
                    </Button>
                </Stack>

                {/* Current access */}
                <Typography
                    variant="subtitle2"
                    fontWeight={700}
                    sx={{ mt: 3, mb: 1 }}
                >
                    Who has access ({accesses.length})
                </Typography>
                <Stack spacing={1}>
                    {accesses.length === 0 && (
                        <Typography variant="body2" color="text.secondary">
                            Not shared with anyone yet.
                        </Typography>
                    )}
                    {accesses.map((a) => {
                        const l = getAccessLevel(a.access_level);
                        return (
                            <Stack
                                key={a.id}
                                direction="row"
                                spacing={1.5}
                                sx={{
                                    p: 1,
                                    alignItems: "center",
                                    border: "1px solid",
                                    borderColor: "divider",
                                    borderRadius: 3,
                                }}
                            >
                                <Avatar sx={{ width: 32, height: 32 }}>
                                    {a.user_group_id ? (
                                        <GroupsIcon fontSize="small" />
                                    ) : (
                                        <PersonIcon fontSize="small" />
                                    )}
                                </Avatar>
                                <Typography
                                    variant="body2"
                                    fontWeight={600}
                                    noWrap
                                    sx={{ flex: 1 }}
                                >
                                    {a.name}
                                </Typography>
                                <TextField
                                    select
                                    size="small"
                                    value={a.access_level}
                                    onChange={(e) =>
                                        onChangeLevel?.(a, e.target.value)
                                    }
                                    sx={{ minWidth: 130 }}
                                    color={l?.color}
                                >
                                    {ACCESS_LEVELS.map((opt) => (
                                        <MenuItem
                                            key={opt.value}
                                            value={opt.value}
                                        >
                                            {opt.label}
                                        </MenuItem>
                                    ))}
                                </TextField>
                                <Tooltip title="Revoke access">
                                    <IconButton
                                        size="small"
                                        onClick={() => onRevoke?.(a)}
                                    >
                                        <DeleteOutlinedIcon fontSize="small" />
                                    </IconButton>
                                </Tooltip>
                            </Stack>
                        );
                    })}
                </Stack>

                <Box
                    sx={{ display: "flex", justifyContent: "flex-end", mt: 2 }}
                >
                    <CButtonClose onClick={handleClose} />
                </Box>
            </CModal>
        </>
    );
};

export default ShareCredential;
