import { CBoxContent, CSelect } from "@/Components";
import { iconMap } from "@/Utilities/icons";

import UserGroupContent from "@/Components/Pages/UserGroup/UserGroupContent";
import RoleContent from "@/Components/Pages/Role/RoleContent";
import PermissionContent from "@/Components/Pages/Permission/PermissionContent";
import ModuleContent from "@/Components/Pages/Module/ModuleContent";

import {
    Tabs,
    Tab,
    Box,
    FormControl,
    Select,
    MenuItem,
    useTheme,
    useMediaQuery,
} from "@mui/material";

import { useEffect, useState } from "react";

const DRAWER_WIDTH = 200;
const DEFAULT_FLASH = {
    success: null,
    error: null,
    info: null,
    warning: null,
};

const DEFAULT_ERRORS = {};

const SettingContent = ({
    flash,
    errors,
    can,
    userGroupTypes,
    permissions,
    moduleLists,
    accessibleRoutes,
    settingsModules,
    categories,
    permissionsByModule,
}) => {
    const [value, setValue] = useState(0);
    const [showMessages, setShowMessages] = useState(true);

    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down("md"));

    useEffect(() => {
        setShowMessages(true);
    }, [flash, errors]);

    const handleTabChange = (event, newValue) => {
        setValue(newValue);
        setShowMessages(false);
    };

    // list of contents for each setting module
    const settingTabContents = [
        {
            base_name: "user_groups",
            component: (
                <UserGroupContent
                    flash={showMessages ? flash : DEFAULT_FLASH}
                    errors={showMessages ? errors : DEFAULT_ERRORS}
                    can={can}
                    userGroupTypes={userGroupTypes}
                />
            ),
        },
        {
            base_name: "roles",
            component: (
                <RoleContent
                    flash={showMessages ? flash : DEFAULT_FLASH}
                    errors={showMessages ? errors : DEFAULT_ERRORS}
                    permissions={permissions}
                    moduleLists={moduleLists}
                    can={can}
                />
            ),
        },
        {
            base_name: "permissions",
            component: (
                <PermissionContent
                    flash={showMessages ? flash : DEFAULT_FLASH}
                    errors={showMessages ? errors : DEFAULT_ERRORS}
                    permissionsByModule={permissionsByModule}
                    can={can}
                />
            ),
        },
        // {
        //     base_name: "modules",
        //     component: (
        //         <ModuleContent
        //             flash={showMessages ? flash : DEFAULT_FLASH}
        //             errors={showMessages ? errors : DEFAULT_ERRORS}
        //             can={can}
        //             categories={categories}
        //         />
        //     ),
        // },
    ];

    // Map settings modules to their corresponding tab content
    const tabs = settingsModules.map((module) => {
        const tabContent = settingTabContents.find(
            (content) => content.base_name === module.base_name,
        );

        return {
            label: module.name,
            icon: module.icon,
            component: tabContent ? tabContent.component : null,
        };
    });

    // Filter tabs based on accessible routes
    const accessibleTabs = tabs.filter((tab) =>
        accessibleRoutes.some((route) =>
            route.includes(tab.label.toLowerCase().replace(" ", "_")),
        ),
    );

    return (
        <CBoxContent>
            <Box
                sx={{
                    display: "flex",
                    flexDirection: isMobile ? "column" : "row",
                    width: "100%",
                    gap: 2,
                }}
            >
                {/* Mobile: Dropdown */}
                {isMobile ? (
                    <FormControl fullWidth>
                        <CSelect
                            options={accessibleTabs.map((tab, index) => ({
                                value: index,
                                label: tab.label,
                            }))}
                            value={value}
                            onChange={(e) =>
                                handleTabChange(null, e.target.value)
                            }
                            label="Select Setting"
                        />
                    </FormControl>
                ) : (
                    /* Desktop: Vertical Tabs */
                    <Box
                        sx={{
                            width: 220,
                            flexShrink: 0,
                            borderRight: 1,
                            borderColor: "divider",
                        }}
                    >
                        <Tabs
                            orientation="vertical"
                            value={value}
                            onChange={handleTabChange}
                            sx={{
                                "& .MuiTab-root": {
                                    justifyContent: "flex-start",
                                    alignItems: "stretch",
                                    minHeight: 48,
                                    textTransform: "none",
                                    px: 2,
                                },
                            }}
                        >
                            {accessibleTabs.map((tab, index) => {
                                const Icon = iconMap[tab.icon];

                                return (
                                    <Tab
                                        key={index}
                                        value={index}
                                        disableRipple
                                        label={
                                            <Box
                                                sx={{
                                                    display: "flex",
                                                    alignItems: "center",
                                                    justifyContent:
                                                        "flex-start",
                                                    gap: 1.25,
                                                    width: "100%",
                                                }}
                                            >
                                                <Icon
                                                    fontSize="small"
                                                    sx={{
                                                        flexShrink: 0,
                                                        color: "inherit",
                                                        transition:
                                                            "transform 0.2s ease",
                                                    }}
                                                />

                                                <Box
                                                    component="span"
                                                    sx={{
                                                        fontSize: 14,
                                                        fontWeight: 600,
                                                        lineHeight: 1,
                                                    }}
                                                >
                                                    {tab.label}
                                                </Box>
                                            </Box>
                                        }
                                        sx={{
                                            minHeight: 42,
                                            px: 2,
                                            py: 1,
                                            alignItems: "flex-start",
                                            textAlign: "left",

                                            borderRadius: 2,
                                            color: "text.primary",
                                            fontWeight: 600,
                                            textTransform: "none",

                                            transition:
                                                "color 0.2s ease, background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease",

                                            "&:hover": {
                                                color: "#fff",
                                                backgroundColor: "primary.main",
                                                transform: "translateY(-2px)",
                                                boxShadow: (theme) =>
                                                    `0 5px 14px ${theme.palette.primary.main}30`,

                                                "& .MuiSvgIcon-root": {
                                                    transform:
                                                        "scale(1.12) rotate(-4deg)",
                                                },
                                            },

                                            "&.Mui-selected": {
                                                color: "#fff",
                                                background: (theme) =>
                                                    theme.palette.gradients
                                                        .primary,
                                                boxShadow: (theme) =>
                                                    `0 4px 12px ${theme.palette.primary.main}35`,
                                            },

                                            "&.Mui-selected:hover": {
                                                background: (theme) =>
                                                    theme.palette.gradients
                                                        .primaryHover,
                                                transform: "translateY(-2px)",
                                                boxShadow: (theme) =>
                                                    `0 7px 18px ${theme.palette.primary.main}45`,
                                            },

                                            "&:focus-visible": {
                                                outline: "2px solid",
                                                outlineColor: "primary.main",
                                                outlineOffset: 2,
                                            },
                                        }}
                                    />
                                );
                            })}
                        </Tabs>
                    </Box>
                )}

                {/* Content */}
                <Box
                    sx={{
                        flex: 1,
                        minWidth: 0,
                        overflow: "auto",
                    }}
                >
                    {accessibleTabs[value].component}
                </Box>
            </Box>
        </CBoxContent>
    );
};

export default SettingContent;
