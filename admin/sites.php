<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$config = require __DIR__ . "/../config.php";

// Get existing sites
try {
    $dsn = "mysql:host={$config["database"]["host"]};port={$config["database"]["port"]};dbname={$config["database"]["name"]};charset=utf8mb4";
    $pdo = new PDO($dsn, $config["database"]["user"], $config["database"]["password"]);
    
    $sites = $pdo->query("SELECT * FROM sites ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $sites = [];
}

$showNewSiteForm = isset($_GET["action"]) && $_GET["action"] === "new";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Sites - Beefus CMS</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: #1a1a1a; padding: 25px; border-radius: 12px; margin: 20px 0; }
        .card h2 { color: #ff6b6b; margin-bottom: 20px; }
        .sites-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .site-card { background: #2a2a2a; padding: 20px; border-radius: 8px; }
        .site-card h3 { color: #ff6b6b; margin-bottom: 10px; }
        .site-card .domain { color: #999; font-size: 0.9rem; margin-bottom: 15px; }
        .site-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 15px 0; }
        .stat { text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: bold; color: #ff6b6b; }
        .stat-label { font-size: 0.8rem; color: #999; }
        .btn { padding: 10px 20px; background: #ff6b6b; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #ff5252; }
        .btn-secondary { background: #3a3a3a; }
        .btn-secondary:hover { background: #4a4a4a; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #ff6b6b; margin-bottom: 8px; }
        input[type="text"], input[type="color"], select { width: 100%; padding: 12px; background: #2a2a2a; border: 1px solid #3a3a3a; color: #e0e0e0; border-radius: 6px; }
        .back-link { color: #999; }
        .back-link:hover { color: #ff6b6b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌐 Manage Sites</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <?php if ($showNewSiteForm): ?>
            <div class="card">
                <h2>Deploy New Site</h2>
                <form method="POST" action="deploy-site.php">
                    <div class="form-group">
                        <label>Domain Name</label>
                        <input type="text" name="domain" placeholder="example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" name="name" placeholder="My Video Site" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tagline</label>
                        <input type="text" name="tagline" placeholder="Watch the best videos online">
                    </div>
                    
                    <div class="form-group">
                        <label>Primary Color</label>
                        <input type="color" name="primary_color" value="#ff6b6b">
                    </div>
                    
                    <div class="form-group">
                        <label>Ranking Mode</label>
                        <select name="ranking_mode">
                            <option value="random">Random</option>
                            <option value="views">By Views</option>
                            <option value="rating">By Rating</option>
                            <option value="clicks">By Clicks</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Deploy Site</button>
                    <a href="sites.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 20px;">
                <a href="sites.php?action=new" class="btn">Deploy New Site</a>
            </div>
            
            <?php if (empty($sites)): ?>
                <div class="card">
                    <h2>No Sites Yet</h2>
                    <p>You haven't deployed any sites yet. Click "Deploy New Site" to get started!</p>
                </div>
            <?php else: ?>
                <div class="sites-grid">
                    <?php foreach ($sites as $site): ?>
                        <div class="site-card">
                            <h3><?php echo htmlspecialchars($site["name"]); ?></h3>
                            <div class="domain"><?php echo htmlspecialchars($site["domain"]); ?></div>
                            
                            <div class="site-stats">
                                <div class="stat">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">Videos</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">Views</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-value">0%</div>
                                    <div class="stat-label">CTR</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <a href="edit-site.php?id=<?php echo $site["id"]; ?>" class="btn btn-secondary">Edit</a>
                                <a href="http://<?php echo htmlspecialchars($site["domain"]); ?>" target="_blank" class="btn btn-secondary">Visit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>