<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$config = require __DIR__ . "/../config.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["csv_file"])) {
    $uploadDir = __DIR__ . "/../storage/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadFile = $uploadDir . basename($_FILES["csv_file"]["name"]);
    
    if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $uploadFile)) {
        $message = "File uploaded successfully. Import functionality coming soon!";
        // TODO: Implement actual import logic
    } else {
        $message = "Upload failed!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import Videos - Beefus CMS</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; margin: 0; }
        .header { background: #1a1a1a; padding: 20px; border-bottom: 1px solid #2a2a2a; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #ff6b6b; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: #1a1a1a; padding: 25px; border-radius: 12px; margin: 20px 0; }
        .card h2 { color: #ff6b6b; margin-bottom: 20px; }
        .upload-area { border: 2px dashed #3a3a3a; border-radius: 8px; padding: 40px; text-align: center; transition: all 0.3s ease; }
        .upload-area:hover { border-color: #ff6b6b; background: rgba(255, 107, 107, 0.05); }
        input[type="file"] { margin: 20px 0; }
        .btn { padding: 12px 24px; background: #ff6b6b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 600; }
        .btn:hover { background: #ff5252; }
        .message { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .message.success { background: rgba(76, 175, 80, 0.2); color: #4caf50; }
        .message.error { background: rgba(244, 67, 54, 0.2); color: #f44336; }
        a { color: #ff6b6b; text-decoration: none; }
        .back-link { color: #999; }
        .back-link:hover { color: #ff6b6b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📥 Import Videos</h1>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Upload CSV File</h2>
            <p>Upload a CSV file exported from the video scraper tool.</p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="upload-area">
                    <p>📁 Choose CSV file to upload</p>
                    <input type="file" name="csv_file" accept=".csv" required>
                    <br><br>
                    <button type="submit" class="btn">Upload & Import</button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Expected CSV Format</h2>
            <p>Your CSV should contain these columns:</p>
            <ul>
                <li>title - Video title</li>
                <li>url - Video URL</li>
                <li>embed_code - Embed iframe src</li>
                <li>duration - Video duration</li>
                <li>views - View count</li>
                <li>rating - Rating percentage</li>
                <li>uploader - Channel name</li>
                <li>tags - Comma-separated tags</li>
                <li>thumbnail_url - Thumbnail image URL</li>
                <li>scraped_at - Scraping timestamp</li>
            </ul>
        </div>
    </div>
</body>
</html>