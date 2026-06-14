<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih User - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg-yellow: #fff8f3;
            --card-bg: #ffffff;
            --primary: #354024;
            --primary-light: #889063;
            --accent: #dae8c0;
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
            max-width: 480px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 48px 40px 40px;
            box-shadow:
                0 24px 60px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .title {
            text-align: center;
            font-size: 32px;
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

        .role-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .role-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 24px;
        }

        .role-button {
            flex: 1;
            width: 100%;
            border: none;
            outline: none;
            border-radius: 999px;
            padding: 14px 20px;
            background: var(--primary);
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.18s ease, transform 0.12s ease, box-shadow 0.18s ease;
            box-shadow: 0 10px 22px rgba(164, 68, 61, 0.32);
        }

        .role-button:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(164, 68, 61, 0.38);
        }

        .role-button:active {
            transform: translateY(0);
            box-shadow: 0 8px 18px rgba(164, 68, 61, 0.28);
        }

        @media (max-width: 520px) {
            .card {
                margin: 24px;
                padding: 32px 24px 28px;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <h1 class="title">Login</h1>
        <p class="subtitle">Silahkan pilih user anda</p>

        <div class="role-row">
            <div class="role-icon"><i class="fa-solid fa-user"></i></div>
            <form action="{{ route('login.role', ['role' => 'admin']) }}" method="get" style="flex:1;">
                <button class="role-button" type="submit">Admin</button>
            </form>
        </div>

        <div class="role-row">
            <div class="role-icon"><i class="fa-solid fa-user"></i></div>
            <form action="{{ route('login.role', ['role' => 'kasir']) }}" method="get" style="flex:1;">
                <button class="role-button" type="submit">Kasir</button>
            </form>
        </div>
    </main>
</body>
</html>


