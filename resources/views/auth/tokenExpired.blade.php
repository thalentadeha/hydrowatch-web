<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, , maximum-scale=1.0">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <script type="module" src=" {{ asset('js/script.js') }}"></script>
    <title>Hydrowatch | Token Expired</title>
</head>

<body>
    <div class="content">
        <div class="text-area">
            <span>Token Expired</span>
            <span class="small-span">Please re-login!</span>
        </div>
        <form action="{{ route('login_GET') }}" method="GET">
            @csrf
            <button type="submit" class="changePassword blue">Re-Login</button>
        </form>
    </div>
</body>
<style>
    body {
        width: 100%;
        height: 100%;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(10px);
    }

    body .content {
        margin: 0;
        top: 50%;
        left: 50%;
        width: 90%;
        height: auto;
        max-width: 800px;
        padding: var(--s6);
        position: absolute;
        transform: translate(-50%, -50%);
        background-color: var(--color-background);
    }

    body .content .text-area {
        width: 100%;
        display: flex;
        gap: var(--s8);
        align-items: center;
        flex-direction: column;
        margin-bottom: var(--s5);
        justify-content: center;
    }

    body .content .text-area span {
        font-size: var(--s4);
        color: var(--color-primary);
        font-family: var(--font-semibold);
        text-align: center;
    }

    body .content .text-area .small-span {
        font-size: var(--s10);
        color: var(--color-primary);
        font-family: var(--font-semibold);
        text-align: center;
    }

    body .content .buttons {
        width: 100%;
        display: flex;
        gap: var(--s9);
        flex-direction: row;
    }

    body .content .desc {
        height: 20svh;
        width: 100%;
        resize: none;
    }

    body button {
        width: 100%;
        padding: var(--s8) 0;
        margin-top: var(--s5);
    }
</style>

</html>
