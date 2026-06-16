<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BongoGames API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a1628;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            text-align: center;
            padding: 40px;
        }
        .pulse {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #d4af37;
            margin: 0 auto 24px;
            animation: pulse-ring 2s ease-out infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.5); }
            70% { box-shadow: 0 0 0 30px rgba(212, 175, 55, 0); }
            100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
        }
        h1 {
            font-size: 2rem;
            color: #ffffff;
            margin-bottom: 8px;
        }
        .status {
            display: inline-block;
            background: rgba(212, 175, 55, 0.15);
            color: #d4af37;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .info {
            color: #8892b0;
            font-size: 0.9rem;
            line-height: 1.8;
        }
        .info strong {
            color: #d4af37;
        }
        .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4caf50;
            margin-right: 6px;
            animation: dot-blink 1.5s ease-in-out infinite;
        }
        @keyframes dot-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pulse"></div>
        <h1>BongoGames API</h1>
        <div class="status"><span class="dot"></span>Operational</div>
        <div class="info">
            <strong>Version</strong> 1.0.0<br>
        </div>
    </div>
</body>
</html>
