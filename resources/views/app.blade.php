<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        @php
            $favicon = asset('favicon.ico');
            try {
                $dbFavicon = \App\Models\Setting::where('key', 'logo_url')->value('value') 
                    ?? \App\Models\Setting::where('key', 'school_logo')->value('value');
                if ($dbFavicon) {
                    $favicon = $dbFavicon;
                }
            } catch (\Exception $e) {
                // Fail silently
            }
        @endphp
        <link rel="icon" href="{{ $favicon }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
