<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    // Optional: delete the image file from server if you want
    // $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
    // $stmt->execute([$id]);
    // $project = $stmt->fetch();
    // if ($project && $project['image'] && file_exists('../' . $project['image'])) {
    //     unlink('../' . $project['image']);
    // }
    
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: projects.php');
exit;