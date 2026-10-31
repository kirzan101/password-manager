// Default fields per category. `shown` fields appear on the credential card.
export const CATEGORY_DEFAULTS = {
    database: [
        { key: "ip", shown: true },
        { key: "port", shown: true },
        { key: "database_type", shown: false },
        { key: "database_name", shown: true },
    ],
    server: [
        { key: "ip", shown: true },
        { key: "os", shown: true },
    ],
    account: [{ key: "url", shown: true }],
    application: [{ key: "url", shown: true }],
    network: [
        { key: "ip", shown: true },
        { key: "device_type", shown: true },
    ],
};

export const defaultFields = (category) =>
    (CATEGORY_DEFAULTS[category] || []).map((f, i) => ({
        id: `${category}-${i}-${Date.now()}`,
        key: f.key,
        value: "",
        shown: f.shown,
    }));

export const normalizeKey = (key) =>
    key
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "_")
        .replace(/^_+|_+$/g, "");

// Convert editable rows into the { data, showed } structure stored by the API.
export const fieldsToProperties = (fields) => {
    const data = {};
    const showed = [];

    fields.forEach(({ key, value, shown }) => {
        const k = normalizeKey(key);
        if (!k) return;
        data[k] = value ?? "";
        if (shown && !showed.includes(k)) showed.push(k);
    });

    return { data, showed };
};

export const formatLabel = (key) =>
    key
        .split("_")
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(" ");

// Parse stored properties (object or JSON string) into editable rows.
export const propertiesToFields = (properties) => {
    let props = properties;
    if (typeof props === "string") {
        try {
            props = JSON.parse(props);
        } catch {
            props = {};
        }
    }

    const data = props?.data && !Array.isArray(props.data) ? props.data : {};
    const showed = Array.isArray(props?.showed) ? props.showed : [];

    return Object.entries(data).map(([key, value], i) => ({
        id: `edit-${i}-${key}`,
        key,
        value: value ?? "",
        shown: showed.includes(key),
    }));
};
