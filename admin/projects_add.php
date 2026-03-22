<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $technologies = $_POST['technologies'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $live_link = $_POST['live_link'] ?? '';
    
    // Handle image upload
    $image = '';
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
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, technologies, github_link, live_link, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$title, $description, $image, $technologies, $github_link, $live_link])) {
            header('Location: projects.php');
            exit;
        } else {
            $error = 'Failed to save project.';
        }
    } else {
        $error = 'Title and description are required.';
    }
}

$pageTitle = 'Add Project';
require_once 'inc/header.php';
?>

<h1>Add New Project</h1>

<?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Technologies (comma separated)</label>
            <input type="text" name="technologies" class="form-control" placeholder="e.g. HTML, CSS, JavaScript">
        </div>
        
        <div class="form-group">
            <label>GitHub Link</label>
            <input type="text" name="github_link" class="form-control" placeholder="https://github.com/...">
        </div>
        
        <div class="form-group">
            <label>Live Demo Link</label>
            <input type="text" name="live_link" class="form-control" placeholder="https://...">
        </div>
        
        <div class="form-group">
            <label>Project Image</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        
        <button type="submit" class="btn">Save Project</button>
        <a href="projects.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>