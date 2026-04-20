<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta
            name="description"
            content="Daily Ops Command Center provides printable operational recap surfaces for university computer lab teams."
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
        @vite(['resources/css/app.css'])
    </head>
    <body class="ops-print-shell">
        <a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2" href="#main-content">
            {{ __('Skip to content') }}
        </a>

        <main id="main-content" class="ops-print-sheet">
            @isset($toolbar)
                <div class="ops-print-toolbar">
                    {{ $toolbar }}
                </div>
            @endisset

            {{ $slot }}
        </main>
    </body>
</html>
