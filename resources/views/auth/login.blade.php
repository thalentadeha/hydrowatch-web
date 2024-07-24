<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/icon.png') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <script type="module" src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/firebase.js') }}"></script>
    <title>HydroWatch | Login</title>
</head>
<body>
    <main class="login">
        <section>
            <span class="logo">HydroWatch</span>
            <form method="POST" action="{{  route('login_POST') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-field">
                    <input class="password" type="password" name="password" placeholder="Password" required>
                    <div class="eye">
                        <img class="show" src="{{ asset('img/view.png') }}" alt="">
                        <img class="hide" src="{{ asset('img/hide.png') }}" alt="">
                    </div>
                </div>
                <button type="submit" class="login blue">Login</button>
                <span class="warning">{{ $errors->first() }}</span>
            </form>
        </section>
    </main>
</body>
</html>
