<!DOCTYPE html>
<html lang="es" data-theme="daylight">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
        @routes
        @vite('resources/js/app.js')
        @vite('resources/css/app.css')

        @inertiaHead
    </head>
    <body class=" font-sans antialiased">
        @inertia
    </body>
</html>
