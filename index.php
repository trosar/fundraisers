<?php 
require_once 'ms/db.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scout Fundraiser</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="ms/styles.css?v=<?php echo $styles_version; ?>">
    <link rel="icon" type="image/x-icon" href="ms/media/favicon.svg">
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gtag_version; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo $gtag_version; ?>');
</script>
<body>
<div class="header">
    <a href="https://www.troop60.co/"><img class="troop_logo" src="ms/media/Troop_60_Logo.png" alt="Troop 60 Logo"></a>
    <h2>Troop 60 Fundraisers</h2>
</div>

<div class="main-container">
    <div class="fundraiser-info">
        <br/><br/>
        <h3>Please select a fundraiser below</h3>
        <br/><br/>
        <a href="ms/">
            <p class="btn btn-confirm">Jun/Jul 2026 - Meat Sticks & Chocolate Sales</p>
        </a>
        <br/>
        <a href="fs/">
            <p class="btn btn-confirm">Apr 2026 - Plant Sales</p>
        </a>
        <br/><br/><br/><br/><br/><br/><br/><br/>
        <br/><br/><br/><br/><br/><br/><br/><br/>
        <br/><br/><br/><br/><br/><br/><br/><br/>
    </div>

    <div class="footer-nav">
        <span id="thanks">Thanks for supporting your local scouts!</span>
    </div>
</div>

</body>
</html>

<!-- 


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
            <h2>Jun/Jul 2026 - Meat Sticks & Chocolate</h2>
            <p>Order meat sticks and gourmet chocolate</p>
        </a>
        <a href="fs/" class="store-card fs">
            <h2>Apr 2026 - Plant Sales</h2>
            <p>Order plants and flowers</p>
        </a>
    </div>
</body>
</html> -->