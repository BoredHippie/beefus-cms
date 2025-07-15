<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$config = require __DIR__ . "/../config.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Beefus CMS - Dashboard</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { padding: 20px; }
        .card { background: #1a1a1a; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .card h2 { color: #ff6b6b; }
        a { color: #ff6b6b; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .logout { float: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥩 Beefus CMS Dashboard</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>!</h2>
            <p>Beefus CMS has been successfully installed. This is a basic dashboard.</p>
        </div>
        <div class="card">
            <h2>Next Steps</h2>
            <ul>
                <li>Import videos from CSV files</li>
                <li>Configure categories</li>
                <li>Deploy your first site</li>
                <li>Set up tracking and analytics</li>
            </ul>
        </div>
        <div class="card">
            <h2>System Status</h2>
            <p>PHP Version: <?php echo PHP_VERSION; ?></p>
            <p>Redis: <?php echo $config["redis"]["enabled"] ? "Enabled" : "Disabled"; ?></p>
        </div>
    </div>
</body>
</html>