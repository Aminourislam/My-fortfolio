<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

// Get counts
$blogCount = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

$pageTitle = 'Dashboard';
require_once 'inc/header.php';
?>

<h1>Dashboard</h1>

<div class="card">
    <p>Welcome to the admin panel. Use the sidebar to manage content.</p>
</div>

<div class="card" style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 200px; background: #0f172a; padding: 20px; border-radius: 8px; border: 1px solid #334155;">
        <h3 style="color: #94a3b8; margin-bottom: 10px;">Blog Posts</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: #3b82f6;"><?= $blogCount ?></p>
    </div>
    <div style="flex: 1; min-width: 200px; background: #0f172a; padding: 20px; border-radius: 8px; border: 1px solid #334155;">
        <h3 style="color: #94a3b8; margin-bottom: 10px;">Projects</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: #3b82f6;"><?= $projectCount ?></p>
    </div>
</div>


<?php require_once 'inc/footer.php'; ?>