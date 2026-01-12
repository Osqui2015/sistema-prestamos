<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <style>
            input, select, textarea {
                color: #000000 !important; /* Texto negro siempre */
                background-color: #ffffff !important; /* Fondo blanco siempre */
            }
            /* Para que el texto de ayuda (placeholder) se vea gris y no negro */
            input::placeholder, textarea::placeholder {
                color: #6b7280 !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
