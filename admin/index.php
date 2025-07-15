<?php
session_start();
$config = require __DIR__ . "/../config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $dsn = "mysql:host={$config["database"]["host"]};port={$config["database"]["port"]};dbname={$config["database"]["name"]};charset=utf8mb4";
        $pdo = new PDO($dsn, $config["database"]["user"], $config["database"]["password"]);
        
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
        $stmt->execute(array($_POST["username"]));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($_POST["password"], $user["password_hash"])) {
            $_SESSION["admin_id"] = $user["id"];
            $_SESSION["admin_username"] = $user["username"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Login failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Beefus CMS - Admin Login</title>
    <style>
        body { background: #0f0f0f; color: #e0e0e0; font-family: Arial, sans-serif; }
        .login-box { max-width: 400px; margin: 100px auto; background: #1a1a1a; padding: 40px; border-radius: 12px; }
        h1 { color: #ff6b6b; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #2a2a2a; border: 1px solid #3a3a3a; color: #e0e0e0; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #ff6b6b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        button:hover { background: #ff5252; }
        .error { color: #f44336; text-align: center; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🥩 Beefus CMS</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>