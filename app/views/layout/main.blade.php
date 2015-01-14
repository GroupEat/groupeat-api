<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>{{ $title or 'Groupeat' }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/build/main.css" media="screen, print">
        <style>
        @yield('css')
        </style>
    </head>

    <body>
        @include('partials.navbar')

        <div class="container">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </body>
</body>
</html>
