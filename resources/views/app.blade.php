<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
        (function() {
            const appearance = '{{ $appearance ?? 'system' }}';

            if (appearance === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (prefersDark) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    {{-- Default SEO meta tags (overridden by Inertia Head on specific pages) --}}
    <meta name="description" content="Finsu - Aplikasi pencatatan keuangan pribadi gratis. Lacak pemasukan, pengeluaran, dan analisis pola keuangan Anda." inertia>
    <meta property="og:type" content="website" inertia>
    <meta property="og:title" content="{{ config('app.name', 'Finsu') }} - Aplikasi Pencatatan Keuangan Pribadi" inertia>
    <meta property="og:description" content="Lacak pemasukan, pengeluaran, dan analisis pola keuangan Anda secara visual. Gratis dan mudah digunakan." inertia>
    <meta property="og:image" content="{{ url('/logo/finsu.png') }}" inertia>
    <meta property="og:url" content="{{ url('/') }}" inertia>
    <meta property="og:locale" content="id_ID" inertia>
    <meta property="og:site_name" content="Finsu" inertia>
    <meta name="twitter:card" content="summary_large_image" inertia>
    <meta name="twitter:title" content="{{ config('app.name', 'Finsu') }} - Aplikasi Pencatatan Keuangan Pribadi" inertia>
    <meta name="twitter:description" content="Lacak pemasukan, pengeluaran, dan analisis pola keuangan Anda secara visual. Gratis dan mudah digunakan." inertia>
    <meta name="twitter:image" content="{{ url('/logo/finsu.png') }}" inertia>

    <link rel="icon" href="/logo/finsu.ico" sizes="any">
    <link rel="icon" href="/logo/finsu.png" type="image/png">
    <link rel="apple-touch-icon" href="/logo/finsu.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FinSu">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
