<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Support Ticket System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-wrapper">
        <x-navbar>
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Sign Up</a>
                </li>
            @else
                @if(auth()->user()->isAgent())
                    <li class="nav-item">
                        <a class="link" href="{{ route('tickets.index') }}">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="link" href="{{ route('users.index') }}">Users</a>
                    </li>
                @elseif(auth()->user()->isCustomer())
                    <li class="nav-item">
                        <a class="link" href="{{ route('tickets.index') }}">Tickets</a>
                    </li>
                @endif
                <li class="nav-item d-flex align-items-center">
                    <span class="nav-link disabled">{{ auth()->user()->name }}</span>
                </li>
                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link bg-danger" style="display:inline;cursor:pointer;">Logout <span id="logout-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span></button>
                    </form>
                </li>
            @endguest
        </x-navbar>
    </div>
    <div class="container">
        @yield('content')
    </div>
@stack('scripts')
</body>
</html> 