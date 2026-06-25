<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in &middot; Capili BPO</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ── Left panel ── */
        .left {
            width: 45%;
            background: linear-gradient(145deg, #080820 0%, #101030 50%, #0d0d38 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative arc lines */
        .left::before,
        .left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(0, 192, 192, 0.12);
            pointer-events: none;
        }

        .left::before {
            width: 600px;
            height: 600px;
            bottom: -180px;
            right: -200px;
        }

        .left::after {
            width: 420px;
            height: 420px;
            bottom: -80px;
            right: -100px;
        }

        .arc1,
        .arc2 {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(0, 192, 192, 0.08);
            pointer-events: none;
        }

        .arc1 {
            width: 780px;
            height: 780px;
            bottom: -280px;
            right: -300px;
        }

        .arc2 {
            width: 240px;
            height: 240px;
            bottom: 20px;
            right: 20px;
        }

        .left-logo img {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        .left-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 0 20px;
        }

        .left-body h1 {
            font-size: 42px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .left-body p {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.65;
            max-width: 320px;
        }

        .left-footer {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.3);
        }

        /* ── Right panel ── */
        .right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 72px;
            background: #fff;
        }

        .right-brand {
            font-size: 16px;
            font-weight: 700;
            color: #101030;
            margin-bottom: 52px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .right-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .right h2 {
            font-size: 28px;
            font-weight: 800;
            color: #101030;
            margin-bottom: 8px;
        }

        .right .sub {
            font-size: 13.5px;
            color: #7a8a96;
            margin-bottom: 36px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #101030;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .field input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #dde3ea;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #101030;
            background: #fafbfc;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .field input:focus {
            outline: none;
            border-color: #00b0b0;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 176, 176, 0.12);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #101030;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.18s;
            margin-top: 6px;
        }

        .btn-login:hover {
            background: #00b0b0;
        }

        .err {
            background: #fbeae6;
            color: #b8412f;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid #b8412f;
        }

        /* ── Responsive ── */
        @media (max-width: 800px) {
            body {
                flex-direction: column;
            }

            .left {
                width: 100%;
                min-height: 280px;
                padding: 36px 32px;
            }

            .left-body h1 {
                font-size: 30px;
            }

            .right {
                padding: 40px 32px;
            }
        }
    </style>
</head>

<body>

    {{-- ── Left panel ── --}}
    <div class="left">
        <div class="arc1"></div>
        <div class="arc2"></div>

        <div class="left-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Capili BPO">
        </div>

        <div class="left-body">
            <h1>Hello,<br>Capili BPO! <iconify-icon icon="material-symbols:waving-hand"
                    style="font-size:38px; vertical-align:-6px; color:#00c0c0;"></iconify-icon></h1>
            <p>Track your team's shifts and tasks in real time. Stay on top of productivity every single day.</p>
        </div>

        <div class="left-footer">
            &copy; {{ date('Y') }} Capili Business Process Outsourcing Services. All rights reserved.
        </div>
    </div>

    {{-- ── Right panel ── --}}
    <div class="right">
        <div class="right-brand">
            <img src="{{ asset('images/logo.png') }}" alt="">
            Capili BPO Tracker
        </div>

        <h2>Welcome Back!</h2>
        <p class="sub">Log in to start tracking your task time.</p>

        @if ($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    placeholder="you@example.com" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Log in Now</button>
        </form>
    </div>

</body>

</html>
