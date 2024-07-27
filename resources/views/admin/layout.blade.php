<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1.0">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <script type="module" src="{{ asset('js/script.js') }}"></script>
    <title>HydroWatch | Admin</title>
</head>
<body>
    <dialog data-modal>
    </dialog>
    <header>
        <nav>
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="">
            <menu class="">
                <ul>
                    <li class="{{ request()->routeIs('admin-dashboard') ? 'active' : ''}}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('admin-dashboard-pass-token') }}">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin-setting') ? 'active' : ''}}">
                        <img class="arrow" src="{{ asset('img/arrow-1.png') }}" alt="">
                        <a href="{{ route('admin-setting-pass-token') }}">
                            <span>Setting</span>
                        </a>
                    </li>
                </ul>
                <img class="close" src="{{ asset('img/close.png') }}" alt="">
            </menu>
            <div id="profile">
                <div class="names">
                    <span class="nickname">Admin</span>
                    <span class="full-name">Administrator</span>
                </div>
                <img class="avatar" src="{{ asset('img/no-avatar.png') }}" alt="">
            </div>
        </nav>
    </header>
    @yield('content')

    @yield('script')
</body>
</html>
