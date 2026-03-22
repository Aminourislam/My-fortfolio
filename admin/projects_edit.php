<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) {
    header('Location: projects.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $technologies = $_POST['technologies'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $live_link = $_POST['live_link'] ?? '';
    
    // Handle image upload
    $image = $project['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'uploads/' . $fileName;
        } else {
            $error = 'Failed to upload image.';
        }
    }
    
    if (!$error && $title && $description) {
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, image = ?, technologies = ?, github_link = ?, live_link = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $image, $technologies, $github_link, $live_link, $id])) {
            $message = 'Project updated successfully.';
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update project.';
        }
    } else {
        $error = 'Title and description are required.';
    }
}

$pageTitle = 'Edit Project';
require_once 'inc/header.php';
?>

<h1>Edit Project</h1>

<?php if ($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($project['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Technologies (comma separated)</label>
            <input type="text" name="technologies" value="<?= htmlspecialchars($project['technologies']) ?>" class="form-control" placeholder="e.g. HTML, CSS, JavaScript">
        </div>
        
        <div class="form-group">
            <label>GitHub Link</label>
            <input type="text" name="github_link" value="<?= htmlspecialchars($project['github_link']) ?>" class="form-control" placeholder="https://github.com/...">
        </div>
        
        <div class="form-group">
            <label>Live Demo Link</label>
            <input type="text" name="live_link" value="<?= htmlspecialchars($project['live_link']) ?>" class="form-control" placeholder="https://...">
        </div>
        
        <div class="form-group">
            <label>Current Image</label>
            <?php if ($project['image']): ?>
                <div><img src="../<?= htmlspecialchars($project['image']) ?>" class="current-img"></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Upload New Image (leave empty to keep current)</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        
        <button type="submit" class="btn">Update Project</button>
        <a href="projects.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>