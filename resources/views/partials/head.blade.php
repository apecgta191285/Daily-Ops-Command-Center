<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta
    name="description"
    content="Daily Ops Command Center is an internal web app for university computer lab teams, combining daily checks, issue follow-up, and lightweight administration in one place."
/>

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Daily Ops Command Center') : config('app.name', 'Daily Ops Command Center') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link
    rel="preload"
    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|manrope:500,600,700,800&display=swap"
    as="style"
    onload="this.onload=null;this.rel='stylesheet'"
>
<noscript>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|manrope:500,600,700,800&display=swap" rel="stylesheet" />
</noscript>

@vite(['resources/css/app.css', 'resources/js/app.js'])
