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
            overflow: hidden;
        }
        .container {
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .orbit {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 32px;
        }
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid transparent;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .ring-1 {
            width: 140px;
            height: 140px;
            border-top-color: #d4af37;
            border-right-color: rgba(212, 175, 55, 0.2);
            animation: spin 2.5s linear infinite;
        }
        .ring-2 {
            width: 100px;
            height: 100px;
            border-bottom-color: #d4af37;
            border-left-color: rgba(212, 175, 55, 0.2);
            animation: spin-reverse 2s linear infinite;
        }
        .core {
            width: 60px;
            height: 60px;
            background: radial-gradient(circle at 30% 30%, #f0c040, #d4af37 40%, #b8960f);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
            animation: pulse-core 1.8s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        @keyframes spin-reverse {
            to { transform: translate(-50%, -50%) rotate(-360deg); }
        }
        @keyframes pulse-core {
            0%, 100% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 20px rgba(212, 175, 55, 0.5); }
            50% { transform: translate(-50%, -50%) scale(1.1); box-shadow: 0 0 40px rgba(212, 175, 55, 0.8); }
        }
        h1 {
            font-size: 2.2rem;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .status {
            display: inline-flex;
            align-items: center;
            background: rgba(212, 175, 55, 0.12);
            color: #d4af37;
            padding: 6px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 28px;
            border: 1px solid rgba(212, 175, 55, 0.25);
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #4caf50;
            margin-right: 10px;
            box-shadow: 0 0 8px #4caf50;
            animation: dot-blink 1.2s ease-in-out infinite;
        }
        @keyframes dot-blink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }
        .info {
            color: #8892b0;
            font-size: 0.95rem;
            line-height: 2;
        }
        .info strong {
            color: #d4af37;
            font-weight: 500;
        }
        .uptime {
            display: inline-block;
            margin-top: 16px;
            color: #ffffff;
            font-size: 0.9rem;
            font-variant-numeric: tabular-nums;
        }
        .uptime span {
            color: #d4af37;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="orbit">
            <div class="ring ring-1"></div>
            <div class="ring ring-2"></div>
            <div class="core"></div>
        </div>
        <h1>BongoGames API</h1>
        <div class="status">
            <span class="dot"></span>
            Operational
        </div>
        <div class="info">
            <strong>Version</strong> 1.0.0
            <div class="uptime" id="uptime">
                Uptime: <span>00:00:00</span>
            </div>
        </div>
    </div>

    <script>
        const start = new Date();
        function updateUptime() {
            const diff = Math.floor((new Date() - start) / 1000);
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            document.getElementById('uptime').querySelector('span').textContent = h + ':' + m + ':' + s;
        }
        setInterval(updateUptime, 1000);
        updateUptime();
    </script>
</body>
</html>
