<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Doosoun AOS</title>
        <link rel="icon" id="favicon" type="image/x-icon" href="favicon.ico">
        <link rel="manifest" href="/manifest.webmanifest">
    </head>
    <body>
        <div id="app">
            <app />
        </div>
        <script src="{{ mix('/js/build/app.js') }}"></script>
        @if (request()->path() != 'login')
            <!-- PC payment window only (not required for mobile payment window)-->
            <script src="https://web.nicepay.co.kr/v3/webstd/js/nicepay-3.0.js" type="text/javascript"></script>
        @endif
        <script src="{{ asset('/js/script.js') }}"></script>
    </body>
</html>
