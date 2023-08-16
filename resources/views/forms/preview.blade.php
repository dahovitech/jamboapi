<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="font-sans antialiased">
        <div class="w-1/2 m-auto p-4 h-screen">
            <div class="text-3xl mt-3">{{ env('APP_NAME') }}</div>

            <div class="flex justify-between mt-10">
                <div class="whitespace-nowrap w-1/3">
                    <h3 class="text-gray-500">Embed Form</h3>
                    <h2 class="text-2xl font-bold">{{ $form->name }}</h2>
                </div>
                <div class=" w-2/3 pl-10">
                    <textarea class="min-h-12 border-0 bg-gray-800 rounded-md p-4 text-gray-100 w-full h-32" readonly><iframe width="100%" height="100%"  style="border:none;" src="{{ $embed_url }}"></iframe></textarea>
                </div>
            </div>

            <div class="mt-10 p-4 bg-gray-50" style="height: 550px;">
                <h2 class="text-xl font-bold">Preview</h2>

                <div class="mt-5 pb-12 h-full">
                    <iframe width="100%" height="100%"  style="border:none; background: #fff" src="{{ $embed_url }}"></iframe>
                </div>
            </div>
        </div>
    </body>
</html>
