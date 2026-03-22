<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$error = '';
$title = $content = $created_at_input = ''; // initialize for form repopulation

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $created_at_input = $_POST['created_at'] ?? '';
    
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
    
    // Handle custom date
    $created_at_value = null;
    if (!empty($created_at_input)) {
        $date = DateTime::createFromFormat('Y-m-d', $created_at_input);
        if ($date) {
            $created_at_value = $date->format('Y-m-d H:i:s'); // time = 00:00:00
        } else {
            $error = 'Invalid date format. Please use YYYY-MM-DD.';
        }
    }
    
    if (!$error && $title && $content) {
        if ($created_at_value) {
            // Use the custom date
            $stmt = $pdo->prepare("INSERT INTO blogs (title, content, image, created_at) VALUES (?, ?, ?, ?)");
            $params = [$title, $content, $image, $created_at_value];
        } else {
            // Use current timestamp
            $stmt = $pdo->prepare("INSERT INTO blogs (title, content, image, created_at) VALUES (?, ?, ?, NOW())");
            $params = [$title, $content, $image];
        }
        
        if ($stmt->execute($params)) {
            header('Location: blogs.php');
            exit;
        } else {
            $error = 'Failed to save blog.';
        }
    } else {
        if (!$title) $error = 'Title is required.';
        elseif (!$content) $error = 'Content is required.';
    }
} // <-- This closes the POST block

$pageTitle = 'Add Blog';
require_once 'inc/header.php';
?>

<h1>Add New Blog</h1>

<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required
                   value="<?= htmlspecialchars($title) ?>">
        </div>
        
        <div class="form-group">
            <label>Content (HTML allowed)</label>
            <textarea name="content" class="form-control" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Featured Image</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label>Publish Date (optional – leave blank for current date)</label>
            <input type="date" name="created_at" class="form-control"
                   value="<?= htmlspecialchars($created_at_input) ?>">
        </div>
        
        <button type="submit" class="btn">Save Blog</button>
        <a href="blogs.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>