import { useState, useRef, useEffect } from "react";
import {
    Avatar,
    Box,
    Button,
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    Slider,
    Stack,
    Typography,
} from "@mui/material";
import PhotoCameraIcon from "@mui/icons-material/PhotoCamera";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";
import { router } from "@inertiajs/react";

const CROP_SIZE = 280;
const OUTPUT_SIZE = 750; // change this if you want a different output size (in px)

/**
 * AvatarUpload
 *
 * Displays a clickable avatar that opens a crop/resize dialog on click.
 *
 * Props:
 *  - avatarUrl  {string}                  Current avatar URL shown in the trigger.
 *  - initials   {string}                  Fallback text shown when avatarUrl is empty.
 *  - onChange   {(blob, dataUrl) => void} Called with the cropped Blob and a JPEG data URL.
 *  - size       {number}                  Diameter of the trigger avatar in px. Default: 96.
 *  - disabled   {boolean}                 Disables upload interaction. Default: false.
 *  - profileId  {number|null}             ID of the profile associated with the avatar. Default: null.
 *  - onSuccess  {() => void}                Callback invoked after a successful avatar change. Default: empty function.
 */
const AvatarUpload = ({
    avatarUrl,
    initials,
    onChange,
    size = 96,
    disabled = false,
    profileId = null,
    onSuccess = () => {},
    avatarSx,
}) => {
    const [open, setOpen] = useState(false);
    const [viewOpen, setViewOpen] = useState(false);
    const [imageSrc, setImageSrc] = useState(null);
    const [zoom, setZoom] = useState(1);
    const [minZoom, setMinZoom] = useState(1);
    const [offset, setOffset] = useState({ x: 0, y: 0 });
    const [isDragging, setIsDragging] = useState(false);

    const canvasRef = useRef(null);
    const imageRef = useRef(null);
    const fileInputRef = useRef(null);
    const triggerRef = useRef(null);
    const lastPosRef = useRef({ x: 0, y: 0 });
    const isDraggingRef = useRef(false);
    // Ref mirror of state so event-handler closures always read current values
    const stateRef = useRef({ zoom: 1, offset: { x: 0, y: 0 } });

    useEffect(() => {
        stateRef.current = { zoom, offset };
    });

    // -------------------------------------------------------------------------
    // Canvas helpers
    // -------------------------------------------------------------------------

    const clampOffset = (ox, oy, z, imgW, imgH) => {
        const maxOx = Math.max(0, (imgW * z - CROP_SIZE) / 2);
        const maxOy = Math.max(0, (imgH * z - CROP_SIZE) / 2);
        return {
            x: Math.max(-maxOx, Math.min(maxOx, ox)),
            y: Math.max(-maxOy, Math.min(maxOy, oy)),
        };
    };

    const drawCanvas = (img, z, off) => {
        const canvas = canvasRef.current;
        if (!canvas || !img?.complete || !img?.naturalWidth) return;

        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, CROP_SIZE, CROP_SIZE);

        // Draw the source image
        const scaledW = img.naturalWidth * z;
        const scaledH = img.naturalHeight * z;
        const x = (CROP_SIZE - scaledW) / 2 + off.x;
        const y = (CROP_SIZE - scaledH) / 2 + off.y;
        ctx.drawImage(img, x, y, scaledW, scaledH);

        // Dark overlay with circular cutout (evenodd fill rule)
        ctx.fillStyle = "rgba(0,0,0,0.55)";
        ctx.beginPath();
        ctx.rect(0, 0, CROP_SIZE, CROP_SIZE);
        // Counter-clockwise arc punches a hole via evenodd
        ctx.arc(
            CROP_SIZE / 2,
            CROP_SIZE / 2,
            CROP_SIZE / 2 - 2,
            0,
            Math.PI * 2,
            true,
        );
        ctx.fill("evenodd");

        // Circle border
        ctx.strokeStyle = "rgba(255,255,255,0.85)";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(
            CROP_SIZE / 2,
            CROP_SIZE / 2,
            CROP_SIZE / 2 - 2,
            0,
            Math.PI * 2,
        );
        ctx.stroke();
    };

    // Redraw whenever zoom, offset, or dialog open state changes
    useEffect(() => {
        if (open) drawCanvas(imageRef.current, zoom, offset);
    }, [open, zoom, offset]); // eslint-disable-line react-hooks/exhaustive-deps

    // -------------------------------------------------------------------------
    // File input
    // -------------------------------------------------------------------------

    const handleFileChange = (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        e.target.value = ""; // reset so same file can be re-selected

        // Blur the trigger before opening the dialog so MUI's aria-hidden
        // mechanism does not conflict with a focused element inside #app.
        triggerRef.current?.blur();

        const reader = new FileReader();
        reader.onload = (ev) => {
            setImageSrc(ev.target.result);
            setOpen(true);
        };
        reader.readAsDataURL(file);
    };

    const handleImageLoad = () => {
        const img = imageRef.current;
        if (!img) return;
        const minZ = Math.max(
            CROP_SIZE / img.naturalWidth,
            CROP_SIZE / img.naturalHeight,
        );
        const initialOffset = { x: 0, y: 0 };
        setMinZoom(minZ);
        setZoom(minZ);
        setOffset(initialOffset);
        drawCanvas(img, minZ, initialOffset);
    };

    // -------------------------------------------------------------------------
    // Drag (mouse + touch)
    // -------------------------------------------------------------------------

    const getEventPos = (e) =>
        e.touches
            ? { x: e.touches[0].clientX, y: e.touches[0].clientY }
            : { x: e.clientX, y: e.clientY };

    const handleDragStart = (e) => {
        e.preventDefault();
        isDraggingRef.current = true;
        setIsDragging(true);
        lastPosRef.current = getEventPos(e);
    };

    const handleDragMove = (e) => {
        if (!isDraggingRef.current) return;
        const pos = getEventPos(e);
        const dx = pos.x - lastPosRef.current.x;
        const dy = pos.y - lastPosRef.current.y;
        lastPosRef.current = pos;

        const img = imageRef.current;
        if (!img) return;
        setOffset((prev) =>
            clampOffset(
                prev.x + dx,
                prev.y + dy,
                stateRef.current.zoom,
                img.naturalWidth,
                img.naturalHeight,
            ),
        );
    };

    const handleDragEnd = () => {
        isDraggingRef.current = false;
        setIsDragging(false);
    };

    // -------------------------------------------------------------------------
    // Zoom slider
    // -------------------------------------------------------------------------

    const handleZoomChange = (_, newZoom) => {
        const img = imageRef.current;
        if (!img) return;
        setZoom(newZoom);
        setOffset((prev) =>
            clampOffset(
                prev.x,
                prev.y,
                newZoom,
                img.naturalWidth,
                img.naturalHeight,
            ),
        );
    };

    // -------------------------------------------------------------------------
    // Apply / Cancel
    // -------------------------------------------------------------------------

    const handleApply = () => {
        const img = imageRef.current;
        if (!img?.naturalWidth) return;

        const offscreen = document.createElement("canvas");
        offscreen.width = OUTPUT_SIZE;
        offscreen.height = OUTPUT_SIZE;
        const ctx = offscreen.getContext("2d");

        const scale = OUTPUT_SIZE / CROP_SIZE;
        const scaledW = img.naturalWidth * zoom * scale;
        const scaledH = img.naturalHeight * zoom * scale;
        const x = (OUTPUT_SIZE - scaledW) / 2 + offset.x * scale;
        const y = (OUTPUT_SIZE - scaledH) / 2 + offset.y * scale;
        ctx.drawImage(img, x, y, scaledW, scaledH);

        const dataUrl = offscreen.toDataURL("image/jpeg", 0.92);
        offscreen.toBlob(
            (blob) => {
                if (!blob) return;
                onChange?.(blob, dataUrl);
                setOpen(false);
                setImageSrc(null);
            },
            "image/jpeg",
            0.92,
        );
    };

    const handleCancel = () => {
        setOpen(false);
        setImageSrc(null);
    };

    // Open view dialog if avatar exists, otherwise go straight to file picker
    const handleTriggerClick = () => {
        if (disabled) return;
        triggerRef.current?.blur();
        if (avatarUrl) {
            setViewOpen(true);
        } else {
            fileInputRef.current?.click();
        }
    };

    // Close view dialog then open file picker
    const handleChangePhoto = () => {
        setViewOpen(false);
        requestAnimationFrame(() => fileInputRef.current?.click());
    };

    // Remove photo handler
    const handleRemovePhoto = () => {
        setViewOpen(false);
        onChange?.(null, null);

        // remove url
        let url = "/remove-avatar";

        // If a profileId is provided, update the URL to include it
        if (profileId) {
            url = `/remove-avatar/${profileId}`;
        }

        router.post(
            url,
            {
                _method: "PUT",
            },
            {
                forceFormData: true,
                onSuccess: () => {
                    // Invoke the onSuccess callback after successfully removing the avatar
                    onSuccess?.();
                },
                onError: (errors) => {
                    console.error("Error removing avatar", errors);
                },
            },
        );
    };

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    return (
        <>
            {/* Clickable avatar trigger */}
            <Box
                ref={triggerRef}
                role="button"
                tabIndex={disabled ? -1 : 0}
                aria-label="Upload avatar photo"
                onClick={handleTriggerClick}
                onKeyDown={(e) => {
                    if (!disabled && (e.key === "Enter" || e.key === " ")) {
                        e.preventDefault();
                        handleTriggerClick();
                    }
                }}
                sx={{
                    position: "relative",
                    display: "inline-block",
                    cursor: disabled ? "default" : "pointer",
                    "&:hover .avatar-upload-overlay": {
                        opacity: disabled ? 0 : 1,
                    },
                    "&:focus-visible": {
                        outline: "2px solid",
                        outlineColor: "primary.main",
                        borderRadius: "50%",
                    },
                }}
            >
                <Avatar
                    src={avatarUrl ?? undefined}
                    sx={(theme) => ({
                        width: size,
                        height: size,
                        bgcolor: "primary.main",
                        color: theme.palette.getContrastText(
                            theme.palette.primary.main,
                        ),

                        ...(!avatarUrl && {
                            px: 0.5,
                            fontSize: size * 0.4,
                            fontWeight: 600,
                        }),

                        ...avatarSx,
                    })}
                >
                    {!avatarUrl && initials ? initials : null}
                </Avatar>
                <Box
                    className="avatar-upload-overlay"
                    sx={{
                        position: "absolute",
                        inset: 0,
                        borderRadius: "50%",
                        bgcolor: "rgba(0,0,0,0.5)",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        opacity: 0,
                        transition: "opacity 0.2s ease",
                        pointerEvents: "none",
                    }}
                >
                    {avatarUrl ? (
                        <EditIcon
                            sx={{ color: "#fff", fontSize: size / 2.8 }}
                        />
                    ) : (
                        <PhotoCameraIcon
                            sx={{ color: "#fff", fontSize: size / 2.8 }}
                        />
                    )}
                </Box>
            </Box>

            <input
                ref={fileInputRef}
                type="file"
                accept="image/*"
                style={{ display: "none" }}
                onChange={handleFileChange}
            />

            {/* View image dialog */}
            <Dialog
                open={viewOpen}
                onClose={() => setViewOpen(false)}
                maxWidth="xs"
                fullWidth
            >
                <DialogTitle>Profile Photo</DialogTitle>
                <DialogContent>
                    <Box
                        sx={{
                            display: "flex",
                            justifyContent: "center",
                            py: 2,
                        }}
                    >
                        <Avatar
                            src={avatarUrl}
                            sx={{ width: 200, height: 200 }}
                        />
                    </Box>
                    {!disabled && (
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: "center",
                            }}
                        >
                            <Button
                                startIcon={<DeleteIcon />}
                                color="error"
                                size="small"
                                onClick={handleRemovePhoto}
                                sx={{
                                    m: 1,
                                    background: (theme) =>
                                        theme.palette.gradients.error,
                                    boxShadow: (theme) =>
                                        theme.palette.shadows.errorButton,
                                    transition: "all 0.2s ease",

                                    "&:hover": {
                                        background: (theme) =>
                                            theme.palette.gradients.errorHover,
                                        transform: "translateY(-2px)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .errorButtonHover,
                                    },

                                    "&:active": {
                                        transform: "translateY(0)",
                                        boxShadow: (theme) =>
                                            theme.palette.shadows
                                                .errorButtonActive,
                                    },

                                    "& .MuiButton-startIcon": {
                                        transition: "transform 0.2s ease",
                                    },

                                    "&:hover .MuiButton-startIcon": {
                                        transform: "scale(1.15) rotate(-5deg)",
                                    },
                                }}
                            >
                                Remove Photo
                            </Button>
                        </Box>
                    )}
                </DialogContent>
                <DialogActions>
                    <Button
                        variant="text"
                        onClick={() => setViewOpen(false)}
                        sx={(theme) => ({
                            color:
                                theme.palette.mode === "dark"
                                    ? "#fff"
                                    : theme.palette.primary.main,
                            transition: "all 0.2s ease",
                            "& .MuiButton-startIcon": {
                                transition: "transform 0.2s ease",
                            },

                            "&:hover .MuiButton-startIcon": {
                                transform: "scale(1.15) rotate(-5deg)",
                            },
                        })}
                    >
                        Close
                    </Button>
                    {!disabled && (
                        <Button
                            sx={{
                                background: (theme) =>
                                    theme.palette.gradients.primary,
                                boxShadow: (theme) =>
                                    theme.palette.shadows.primaryButton,
                                transition: "all 0.2s ease",

                                "&:hover": {
                                    background: (theme) =>
                                        theme.palette.gradients.primaryHover,
                                    transform: "translateY(-2px)",
                                    boxShadow: (theme) =>
                                        theme.palette.shadows
                                            .primaryButtonHover,
                                },

                                "&:active": {
                                    transform: "translateY(0)",
                                    boxShadow: (theme) =>
                                        theme.palette.shadows
                                            .primaryButtonActive,
                                },

                                "& .MuiButton-startIcon": {
                                    transition: "transform 0.2s ease",
                                },

                                "&:hover .MuiButton-startIcon": {
                                    transform: "scale(1.15) rotate(-5deg)",
                                },
                            }}
                            variant="contained"
                            onClick={handleChangePhoto}
                        >
                            Change Photo
                        </Button>
                    )}
                </DialogActions>
            </Dialog>

            {/* Crop & resize dialog */}
            <Dialog open={open} onClose={handleCancel} maxWidth="xs" fullWidth>
                <DialogTitle>Crop & Resize Photo</DialogTitle>
                <DialogContent>
                    <Stack spacing={2} sx={{ pt: 1, alignItems: "center" }}>
                        {/* Hidden img element used as the canvas image source */}
                        {imageSrc && (
                            <img
                                ref={imageRef}
                                src={imageSrc}
                                onLoad={handleImageLoad}
                                alt=""
                                style={{ display: "none" }}
                            />
                        )}

                        {/* Drag-to-reposition canvas */}
                        <Box
                            onMouseDown={handleDragStart}
                            onMouseMove={handleDragMove}
                            onMouseUp={handleDragEnd}
                            onMouseLeave={handleDragEnd}
                            onTouchStart={handleDragStart}
                            onTouchMove={handleDragMove}
                            onTouchEnd={handleDragEnd}
                            sx={{
                                cursor: isDragging ? "grabbing" : "grab",
                                borderRadius: 1,
                                overflow: "hidden",
                                userSelect: "none",
                                touchAction: "none",
                                bgcolor: "grey.900",
                                lineHeight: 0,
                            }}
                        >
                            <canvas
                                ref={canvasRef}
                                width={CROP_SIZE}
                                height={CROP_SIZE}
                            />
                        </Box>

                        {/* Zoom slider */}
                        <Stack
                            direction="row"
                            spacing={1.5}
                            sx={{ width: "100%", px: 1, alignItems: "center" }}
                        >
                            <Typography variant="caption" sx={{ minWidth: 36 }}>
                                Zoom
                            </Typography>
                            <Slider
                                min={minZoom}
                                max={minZoom * 3}
                                step={0.001}
                                value={zoom}
                                onChange={handleZoomChange}
                                size="small"
                                aria-label="Zoom"
                            />
                        </Stack>
                    </Stack>
                </DialogContent>
                <DialogActions>
                    <Button
                        variant="text"
                        onClick={handleCancel}
                        sx={(theme) => ({
                            color:
                                theme.palette.mode === "dark"
                                    ? "#fff"
                                    : theme.palette.primary.main,
                        })}
                    >
                        Cancel
                    </Button>
                    <Button variant="contained" onClick={handleApply}>
                        Apply
                    </Button>
                </DialogActions>
            </Dialog>
        </>
    );
};

export default AvatarUpload;
