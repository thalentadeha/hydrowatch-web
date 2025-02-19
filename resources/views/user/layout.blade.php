<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1.0">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="module" src=" {{ asset('js/script.js') }}"></script>
    @yield('head-script')
    <title>HydroWatch | {{ (request()->routeIs('user-dashboard') ? "Home" : (request()->routeIs('user-container') ? "Container" : (request()->routeIs('user-setting') ? "Setting" : ""))) }}</title>
</head>

<body>
    <dialog data-modal>
    </dialog>
    <header>
        <nav>
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="">
            <menu class="">
                <ul>
                    <li class="{{ request()->routeIs('user-dashboard') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-dashboard', ['idToken' => session('idToken')]) }}">
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user-container') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-container', ['idToken' => session('idToken')]) }}">
                            <span>Container</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user-setting') ? 'active' : '' }}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('user-setting', ['idToken' => session('idToken')]) }}">
                            <span>Setting</span>
                        </a>
                    </li>
                </ul>
                <img class="close" src="{{ asset('img/close.png') }}" alt="">
            </menu>
            <div id="profile">
                <div class="names">
                    <span class="nickname">{{ $userData['nickname'] }}</span>
                    <span class="full-name">{{ $userData['fullname'] }}</span>
                </div>
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
            </div>

        </nav>
    </header>
    @yield('content')

    @yield('script')
</body>

</html>
