<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("INSERT INTO blogs (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$title, $content]);

    header('Location: blogs.php'); // was posts.php
    exit;
}
?>