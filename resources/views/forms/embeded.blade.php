<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <script src="{{ mix('js/form.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div id="jamboapiForm" v-cloak><jamboapi-form uuid="{{$form->uuid}}"></jamboapi-form></div>
    </body>
</html>
