<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Manage Projects';
require_once 'inc/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1>Projects</h1>
    <a href="projects_add.php" class="btn"><i class="fas fa-plus"></i> Add New Project</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Technologies</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= $project['id'] ?></td>
                <td>
                    <?php if ($project['image']): ?>
                        <img src="../<?= htmlspecialchars($project['image']) ?>" class="thumb">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($project['title']) ?></td>
                <td><?= htmlspecialchars($project['technologies']) ?></td>
                <td class="actions">
                    <a href="projects_edit.php?id=<?= $project['id'] ?>"><i class="fas fa-edit"></i> Edit</a>
                    <a href="projects_delete.php?id=<?= $project['id'] ?>" class="delete" onclick="return confirm('Delete this project?')"><i class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'inc/footer.php'; ?>