<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$config = require __DIR__ . "/../config.php";

// Get videos with pagination
$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

try {
    $dsn = "mysql:host={$config["database"]["host"]};port={$config["database"]["port"]};dbname={$config["database"]["name"]};charset=utf8mb4";
    $pdo = new PDO($dsn, $config["database"]["user"], $config["database"]["password"]);
    
    $totalCount = $pdo->query("SELECT COUNT(*) FROM videos WHERE status = 'active'")->fetchColumn();
    $totalPages = ceil($totalCount / $perPage);
    
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE status = 'active' ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$perPage, $offset]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $videos = [];
    $totalCount = 0;
    $totalPages = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Videos - Beefus CMS</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .stats { background: #1a1a1a; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-box { text-align: center; }
        .stat-value { font-size: 2rem; color: #ff6b6b; font-weight: bold; }
        .stat-label { color: #999; font-size: 0.9rem; }
        table { width: 100%; background: #1a1a1a; border-radius: 12px; overflow: hidden; }
        th { background: #2a2a2a; padding: 15px; text-align: left; color: #ff6b6b; }
        td { padding: 15px; border-bottom: 1px solid #2a2a2a; }
        tr:hover { background: rgba(255, 107, 107, 0.05); }
        .thumbnail { width: 80px; height: 45px; object-fit: cover; border-radius: 4px; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 30px; }
        .pagination a, .pagination span { padding: 10px 15px; background: #2a2a2a; border-radius: 6px; text-decoration: none; color: #e0e0e0; }
        .pagination a:hover { background: #3a3a3a; }
        .pagination .current { background: #ff6b6b; color: white; }
        .back-link { color: #999; }
        .back-link:hover { color: #ff6b6b; }
        .no-videos { text-align: center; padding: 60px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎬 Manage Videos</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <div class="stats">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($totalCount); ?></div>
                    <div class="stat-label">Total Videos</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo number_format($totalPages); ?></div>
                    <div class="stat-label">Total Pages</div>
                </div>
            </div>
        </div>
        
        <?php if (empty($videos)): ?>
            <div class="no-videos">
                <h2>No Videos Yet</h2>
                <p>Import videos from CSV files to get started.</p>
                <br>
                <a href="import.php" style="padding: 12px 24px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 6px;">Import Videos</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Views</th>
                        <th>Rating</th>
                        <th>Uploader</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($video["thumbnail_url"]); ?>" 
                                     alt="" class="thumbnail" 
                                     onerror="this.src='/assets/img/no-thumb.jpg'">
                            </td>
                            <td><?php echo htmlspecialchars(substr($video["title"], 0, 50)) . "..."; ?></td>
                            <td><?php echo htmlspecialchars($video["duration"]); ?></td>
                            <td><?php echo number_format($video["views"]); ?></td>
                            <td><?php echo htmlspecialchars($video["rating"]); ?>%</td>
                            <td><?php echo htmlspecialchars($video["uploader"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>