<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-yellow: #FFF9C4;
            --card-bg: #ffffff;
            --primary: #A4443D;
            --text-main: #111111;
            --text-sub: #777777;
            --radius-lg: 28px;
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
            max-width: 520px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 40px 36px 32px;
            text-align: center;
            box-shadow:
                0 22px 52px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-sub);
            margin-bottom: 22px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(164, 68, 61, 0.08);
            color: var(--primary);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .primary-btn {
            margin-top: 12px;
            display: inline-block;
            padding: 10px 22px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
        }

        .primary-btn:hover {
            opacity: 0.92;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="pill">
            Berhasil login sebagai {{ ucfirst($role) }}
        </div>

        <h1 class="title">Selamat Datang, {{ $username }}</h1>
        <p class="subtitle">
            Ini hanya halaman demo setelah proses login.<br>
            Di sini nantinya bisa diarahkan ke dashboard {{ ucfirst($role) }}.
        </p>

        <a href="{{ route('select.role') }}" class="primary-btn">
            Kembali ke halaman login
        </a>
    </main>
</body>
</html>


