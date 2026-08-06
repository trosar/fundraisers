<!DOCTYPE html>
<html>
<head>
    <title>Scout Fundraisers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 40px;
            text-align: center;
        }
        .store-links {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .store-card {
            display: block;
            background: #fff;
            border-radius: 12px;
            padding: 40px 50px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            min-width: 250px;
        }
        .store-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .store-card h2 {
            font-size: 1.4rem;
            margin-bottom: 8px;
        }
        .store-card p {
            color: #888;
            font-size: 0.9rem;
        }
        .store-card.ms { border-top: 4px solid #e74c3c; }
        .store-card.fs { border-top: 4px solid #27ae60; }
    </style>
</head>
<body>
    <h1>Scout Fundraisers</h1>
    <p class="subtitle">Support Troop 60 — choose a fundraiser below</p>

    <div class="store-links">
        <a href="ms/" class="store-card ms">
            <h2>🥩 Meat Sticks & Chocolate</h2>
            <p>Order meat sticks and gourmet chocolate</p>
        </a>
        <a href="fs/" class="store-card fs">
            <h2>🌱 Plant Sales</h2>
            <p>Order plants and flowers</p>
        </a>
    </div>
</body>
</html>