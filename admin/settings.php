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
    <title>Settings - Beefus CMS</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: #1a1a1a; padding: 25px; border-radius: 12px; margin: 20px 0; }
        .card h2 { color: #ff6b6b; margin-bottom: 20px; }
        .config-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #2a2a2a; }
        .config-label { color: #999; }
        .config-value { color: #e0e0e0; font-family: monospace; }
        .back-link { color: #999; }
        .back-link:hover { color: #ff6b6b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚙️ Settings</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Database Configuration</h2>
            <div class="config-item">
                <span class="config-label">Host</span>
                <span class="config-value"><?php echo htmlspecialchars($config["database"]["host"]); ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Database Name</span>
                <span class="config-value"><?php echo htmlspecialchars($config["database"]["name"]); ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Status</span>
                <span class="config-value" style="color: #4caf50;">Connected</span>
            </div>
        </div>
        
        <div class="card">
            <h2>Redis Configuration</h2>
            <div class="config-item">
                <span class="config-label">Status</span>
                <span class="config-value"><?php echo $config["redis"]["enabled"] ? "Enabled" : "Disabled"; ?></span>
            </div>
            <?php if ($config["redis"]["enabled"]): ?>
                <div class="config-item">
                    <span class="config-label">Host</span>
                    <span class="config-value"><?php echo htmlspecialchars($config["redis"]["host"]); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Application Settings</h2>
            <div class="config-item">
                <span class="config-label">Application URL</span>
                <span class="config-value"><?php echo htmlspecialchars($config["app"]["url"]); ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Sites Path</span>
                <span class="config-value"><?php echo htmlspecialchars($config["app"]["sites_path"]); ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Thumbnail Mode</span>
                <span class="config-value"><?php echo htmlspecialchars($config["thumbnails"]["mode"] ?? "hotlink"); ?></span>
            </div>
        </div>
        
        <div class="card">
            <h2>System Information</h2>
            <div class="config-item">
                <span class="config-label">PHP Version</span>
                <span class="config-value"><?php echo PHP_VERSION; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Server Software</span>
                <span class="config-value"><?php echo htmlspecialchars($_SERVER["SERVER_SOFTWARE"] ?? "Unknown"); ?></span>
            </div>
        </div>
    </div>
</body>
</html>