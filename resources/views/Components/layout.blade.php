<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Bookfriends</title>
</head>

<body>
    <div class="max-w-4xl mx-auto px-6 grid grid-cols-8 gap-12 mt-16">
        <div class="col-span-2 border-r border-slate-200 space-y-6">
            <div>
                link
            </div>
            <div>
                link
            </div>
            <div>
                link
            </div>
        </div>
        <div class="col-span-6">
            @isset($header)
                <h1 class="text-2xl font-bold text-slate-800">{{ $header }}</h1>
            @endisset

            {{ $slot }}
        </div>
    </div>
</body>
</html>