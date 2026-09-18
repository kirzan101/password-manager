<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @vite('resources/css/app.css')
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>

@production
<script>
    console.log("%cStop!", "color: red; font-size: 40px; font-weight: bold;");

    const isDarkMode =
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches;

    const textColor = isDarkMode ? "#ddd" : "#333";

    console.warn(
        '%cThis is a browser feature intended for developers. If someone told you to copy-paste something here to enable a feature or "hack" an account, it is a scam and will give them access to your account.',
        `font-size: 16px; color: ${textColor}; max-width: 600px;`
    );
</script>
@endproduction