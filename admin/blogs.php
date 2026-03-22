<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Manage Blogs';
require_once 'inc/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1>Blogs</h1>
    <a href="blogs_add.php" class="btn"><i class="fas fa-plus"></i> Add New Blog</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blogs as $blog): ?>
            <tr>
                <td><?= $blog['id'] ?></td>
                <td>
                    <?php if ($blog['image']): ?>
                        <img src="../<?= htmlspecialchars($blog['image']) ?>" class="thumb">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($blog['title']) ?></td>
                <td><?= date('d-m-Y', strtotime($blog['created_at'])) ?></td>
                <td class="actions">
                    <a href="blogs_edit.php?id=<?= $blog['id'] ?>"><i class="fas fa-edit"></i> Edit</a>
                    <a href="blogs_delete.php?id=<?= $blog['id'] ?>" class="delete" onclick="return confirm('Delete this blog?')"><i class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'inc/footer.php'; ?>