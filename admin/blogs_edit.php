<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$blog) {
    header('Location: blogs.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    // Handle image upload
    $image = $blog['image'];
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
    $created_at_input = $_POST['created_at'] ?? '';
    $new_created_at = null;
    
    if (!empty($created_at_input)) {
        $date = DateTime::createFromFormat('Y-m-d', $created_at_input);
        if ($date) {
            $new_created_at = $date->format('Y-m-d H:i:s'); // set time to 00:00:00
        } else {
            $error = 'Invalid date format. Please use YYYY-MM-DD.';
        }
    }
    
    if (!$error && $title && $content) {
        if ($new_created_at !== null) {
            // Update with new date
            $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, image = ?, created_at = ? WHERE id = ?");
            $params = [$title, $content, $image, $new_created_at, $id];
        } else {
            // Keep existing created_at
            $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, image = ? WHERE id = ?");
            $params = [$title, $content, $image, $id];
        }
        
        if ($stmt->execute($params)) {
            $message = 'Blog updated successfully.';
            // Refresh blog data to show updated values in the form
            $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->execute([$id]);
            $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update blog.';
        }
    } else {
        if (!$title) $error = 'Title is required.';
        elseif (!$content) $error = 'Content is required.';
    }
} // <-- This closes the POST block

$pageTitle = 'Edit Blog';
require_once 'inc/header.php';
?>

<h1>Edit Blog</h1>

<?php if ($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Content (HTML allowed)</label>
            <textarea name="content" class="form-control" required><?= htmlspecialchars($blog['content']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Current Image</label>
            <?php if ($blog['image']): ?>
                <div><img src="../<?= htmlspecialchars($blog['image']) ?>" class="current-img"></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Upload New Image (leave empty to keep current)</label>
            <input type="file" name="image" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label>Publish Date (optional – leave blank to keep current date)</label>
            <input type="date" name="created_at" class="form-control"
                   value="<?= $blog['created_at'] ? date('Y-m-d', strtotime($blog['created_at'])) : '' ?>">
        </div>
        
        <button type="submit" class="btn">Update Blog</button>
        <a href="blogs.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>