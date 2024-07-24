<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    {{-- <script src="{{ asset('js/firebase.js') }}"></script> --}}
</head>
<body>
    <h1>Register User</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register_POST') }}" method="POST">
        @csrf

        <div>
            <label for="emp_id">Employee ID:</label>
            <input type="text" id="emp_id" name="emp_id" value="{{ old('emp_id') }}" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div>
            <label for="isAdmin">Admin:</label>
            <input type="checkbox" id="isAdmin" name="isAdmin" value="1" {{ old('isAdmin') ? 'checked' : '' }}>
        </div>

        <div>
            <button type="submit">Register</button>
        </div>
    </form>

    {{-- @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif --}}
</body>
</html>
