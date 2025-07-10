<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Support Ticket System')</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="container mt-4">
        @yield('content')
    </div>
</body>
</html> 