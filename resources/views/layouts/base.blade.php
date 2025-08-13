<!DOCTYPE html>
<html lang="en" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>@yield('title', 'Bookfriends')</title>
    @stack('head')
</head>

<body class="h-full">
    @yield('body')
</body>
</html>