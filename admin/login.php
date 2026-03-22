<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password === '') {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #0b1120; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: #1e293b; padding: 40px; border-radius: 16px; width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
        .login-card h2 { color: #f1f5f9; margin-bottom: 24px; text-align: center; font-weight: 600; }
        .login-card input { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; margin-bottom: 20px; }
        .login-card button { width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .login-card button:hover { background: #2563eb; }
        .error { color: #fecaca; background: #4c1d28; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>