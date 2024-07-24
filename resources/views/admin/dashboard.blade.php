<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <center>
            <H1>
                LOGGED IN!!
            </H1>
        </center>
    </div>
    <div>
        <form method="POST" action="{{  route('logout_POST') }}">
            @csrf

            <button type="submit">sign out</button>
            <span class="warning">{{ $errors->first() }}</span>
        </form>
    </div>
</body>
</html>l
