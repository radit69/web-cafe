<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ ucfirst($role) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-yellow: #fff8f3;
            --card-bg: #ffffff;
            --primary: #354024;
            --primary-light: #889063;
            --input-bg: #faecd8;
            --text-main: #211b0f;
            --text-sub: #45483f;
            --radius-lg: 32px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-yellow);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 100%;
            max-width: 580px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 52px 56px 44px;
            box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .title {
            text-align: center;
            font-size: 34px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 32px;
        }

        .field-group {
            margin-bottom: 18px;
        }

        .label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .input {
            width: 100%;
            border-radius: 12px;
            border: none;
            padding: 12px 16px;
            font-size: 14px;
            background: var(--input-bg);
            outline: none;
            transition: box-shadow 0.15s ease, transform 0.12s ease;
        }

        .input:focus {
            box-shadow: 0 0 0 2px rgba(164, 68, 61, 0.35);
            transform: translateY(-0.5px);
        }

        .error-text {
            margin-top: 4px;
            font-size: 12px;
            color: #d32f2f;
        }

        .btn-submit {
            margin-top: 16px;
            width: 100%;
            border-radius: 999px;
            border: none;
            padding: 14px 20px;
            background: var(--primary);
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.12s ease, box-shadow 0.18s ease;
            box-shadow: 0 12px 24px rgba(164, 68, 61, 0.35);
        }

        .btn-submit:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 16px 32px rgba(164, 68, 61, 0.42);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 10px 20px rgba(164, 68, 61, 0.32);
        }

        .back-link {
            display: inline-block;
            margin-top: 18px;
            font-size: 12px;
            color: var(--text-sub);
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .card {
                margin: 24px 16px;
                padding: 32px 22px 28px;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <h1 class="title">Login</h1>
        <p class="subtitle">
            Silahkan masuk dengan akun anda ({{ ucfirst($role) }})
        </p>

        <form action="{{ route('login.role.submit', ['role' => $role]) }}" method="post">
            @csrf

            <div class="field-group">
                <label class="label" for="username">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    class="input"
                    placeholder="Masukkan nama lengkap"
                    value="{{ old('username') }}"
                    required
                >
                @error('username')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label class="label" for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input"
                    placeholder="Password"
                    required
                >
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>

        <a href="{{ route('select.role') }}" class="back-link">&larr; Kembali ke pilihan user</a>
    </main>
</body>
</html>


