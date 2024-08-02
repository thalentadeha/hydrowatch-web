<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1.0">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="module" src=" {{ asset('js/script.js') }}"></script>
    <title>HydroWatch | Home</title>
</head>

<body>
    <header>
        <nav>
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="">
            <menu class="">
                <ul>
                    <li class="{{ request()->routeIs('user-dashboard') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-dashboard-pass-token') }}">
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user-container') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-container-pass-token') }}">
                            <span>Container</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user-setting') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-setting-pass-token') }}">
                            <span>Setting</span>
                        </a>
                    </li>
                </ul>
                <img class="close" src="{{ asset('img/close.png') }}" alt="">
            </menu>
            {{-- @yield('profile') --}}
            <div id="profile">
                <div class="names">
                    <span class="nickname">{{ $userDoc['nickname'] }}</span>
                    <span class="full-name">{{ $userDoc['fullname'] }}</span>
                </div>
                <img class="avatar" src="asset{{ asset('img/no-avatar.png') }}" alt="">
            </div>

        </nav>
    </header>
    @yield('content')

    @yield('script')
</body>

</html>
