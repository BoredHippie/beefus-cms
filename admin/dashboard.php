<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$config = require __DIR__ . "/../config.php";

// Get some basic stats
try {
    $dsn = "mysql:host={$config["database"]["host"]};port={$config["database"]["port"]};dbname={$config["database"]["name"]};charset=utf8mb4";
    $pdo = new PDO($dsn, $config["database"]["user"], $config["database"]["password"]);
    
    $videoCount = $pdo->query("SELECT COUNT(*) FROM videos WHERE status = 'active'")->fetchColumn();
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1")->fetchColumn();
    $siteCount = $pdo->query("SELECT COUNT(*) FROM sites WHERE is_active = 1")->fetchColumn();
    
    // Get recent imports
    $recentImports = $pdo->query("SELECT * FROM import_logs ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $videoCount = $categoryCount = $siteCount = 0;
    $recentImports = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Beefus CMS - Dashboard</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav-menu { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .nav-menu a { 
            color: #ff6b6b; 
            text-decoration: none; 
            padding: 15px 25px; 
            background: #1a1a1a; 
            border-radius: 8px; 
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .nav-menu a:hover { 
            background: #2a2a2a; 
            border-color: #ff6b6b;
            transform: translateY(-2px);
        }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            background: #1a1a1a; 
            padding: 25px; 
            border-radius: 12px; 
            text-align: center;
            border: 1px solid #2a2a2a;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #ff6b6b;
        }
        .stat-value { font-size: 3rem; color: #ff6b6b; font-weight: bold; }
        .stat-label { color: #999; margin-top: 10px; text-transform: uppercase; font-size: 0.9rem; }
        .card { background: #1a1a1a; padding: 25px; border-radius: 12px; margin: 20px 0; border: 1px solid #2a2a2a; }
        .card h2 { color: #ff6b6b; margin-bottom: 20px; }
        .btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #ff6b6b; 
            color: white; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover { 
            background: #ff5252; 
            transform: translateY(-2px);
        }
        .welcome-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8787 100%);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            color: white;
        }
        .welcome-section h2 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        .recent-activity {
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .activity-header {
            padding: 20px;
            border-bottom: 1px solid #2a2a2a;
        }
        .activity-header h2 {
            color: #ff6b6b;
            margin: 0;
        }
        table { width: 100%; }
        th { 
            background: #2a2a2a; 
            padding: 15px; 
            text-align: left; 
            color: #ff6b6b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td { padding: 15px; border-bottom: 1px solid #2a2a2a; }
        tr:hover { background: rgba(255, 107, 107, 0.05); }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-completed { background: rgba(76, 175, 80, 0.2); color: #4caf50; }
        .status-processing { background: rgba(255, 152, 0, 0.2); color: #ff9800; }
        .status-failed { background: rgba(244, 67, 54, 0.2); color: #f44336; }
        .logout { color: #999; }
        .logout:hover { color: #ff6b6b; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .icon { font-size: 1.2rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥩 Beefus CMS Dashboard</h1>
        <div>
            Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>! | 
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h2>Welcome to Beefus CMS!</h2>
            <p>Your adult video content management system is ready. Start by importing videos, then deploy your first site.</p>
        </div>
        
        <nav class="nav-menu">
            <a href="dashboard.php">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="videos.php">
                <span class="icon">🎬</span>
                <span>Videos</span>
            </a>
            <a href="sites.php">
                <span class="icon">🌐</span>
                <span>Sites</span>
            </a>
            <a href="import.php">
                <span class="icon">📥</span>
                <span>Import</span>
            </a>
            <a href="thumbnails.php">
                <span class="icon">🖼️</span>
                <span>Thumbnails</span>
            </a>
            <a href="settings.php">
                <span class="icon">⚙️</span>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($videoCount); ?></div>
                <div class="stat-label">Total Videos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($categoryCount); ?></div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($siteCount); ?></div>
                <div class="stat-label">Deployed Sites</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Total Views</div>
            </div>
        </div>
        
        <div class="recent-activity">
            <div class="activity-header">
                <h2>Recent Import Activity</h2>
            </div>
            <?php if (empty($recentImports)): ?>
                <div class="empty-state">
                    <p>No imports yet. Start by importing videos from CSV files.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Total Rows</th>
                            <th>Imported</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentImports as $import): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($import["filename"]); ?></td>
                                <td><?php echo number_format($import["total_rows"]); ?></td>
                                <td><?php echo number_format($import["imported"]); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $import["status"]; ?>">
                                        <?php echo ucfirst($import["status"]); ?>
                                    </span>
                                </td>
                                <td><?php echo date("M j, g:i A", strtotime($import["created_at"])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Quick Actions</h2>
            <p>
                <a href="import.php" class="btn">
                    <span class="icon">📥</span> Import Videos
                </a>
                <a href="sites.php?action=new" class="btn">
                    <span class="icon">🚀</span> Deploy New Site
                </a>
                <a href="thumbnails.php" class="btn">
                    <span class="icon">🖼️</span> Manage Thumbnails
                </a>
            </p>
        </div>
        
        <div class="card">
            <h2>System Information</h2>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Redis Status:</strong> <?php echo $config["redis"]["enabled"] ? "✅ Enabled" : "❌ Disabled"; ?></p>
            <p><strong>Thumbnail Mode:</strong> <?php echo ucfirst($config["thumbnails"]["mode"] ?? "hotlink"); ?></p>
            <p><strong>Sites Path:</strong> <?php echo htmlspecialchars($config["app"]["sites_path"]); ?></p>
        </div>
        
        <div class="card">
            <h2>Getting Started</h2>
            <ol style="line-height: 2;">
                <li>📥 <strong>Import Videos:</strong> Upload CSV files from your video scraper</li>
                <li>🖼️ <strong>Configure Thumbnails:</strong> Choose between hotlink, download, or process modes</li>
                <li>🌐 <strong>Deploy Sites:</strong> Create new sites with custom themes and settings</li>
                <li>📊 <strong>Monitor Performance:</strong> Track views, clicks, and CTR across all sites</li>
            </ol>
        </div>
    </div>
</body>
</html>