<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Not Found | Smart Commerce Core</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #070714;
            color: #f0f0ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .wrap { text-align: center; max-width: 420px; }
        .code {
            font-size: 7rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }
        h1 { font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; }
        p { color: #7b7b9d; font-size: 0.9rem; line-height: 1.6; margin-bottom: 28px; }
        a {
            display: inline-block;
            padding: 12px 28px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-radius: 10px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(124,58,237,0.3);
        }
        a:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(124,58,237,0.45); }
    </style>
</head>
<body>
<div class="wrap">
    <div class="code">404</div>
    <h1>Page Not Found</h1>
    <p>The page you're looking for doesn't exist or may have been moved. Check the URL and try again.</p>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>/login" id="btn-404-back">← Go Home</a>
</div>
</body>
</html>
