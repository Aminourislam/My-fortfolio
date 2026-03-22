<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once '../config.php';

// Fetch current about data
$stmt = $pdo->query("SELECT * FROM about LIMIT 1");
$about = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cv_link = $_POST['cv_link'] ?? '';
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $profileImage = 'uploads/' . $fileName;
        } else {
            $error = 'Failed to upload image.';
        }
    } else {
        $profileImage = $about['profile_image'];
    }
    
    if (!$error) {
        $stmt = $pdo->prepare("UPDATE about SET profile_image = ?, cv_link = ? WHERE id = ?");
        if ($stmt->execute([$profileImage, $cv_link, $about['id']])) {
            $message = 'About section updated successfully.';
            // Refresh data
            $stmt = $pdo->query("SELECT * FROM about LIMIT 1");
            $about = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Database update failed.';
        }
    }
}

$pageTitle = 'Manage About';
require_once 'inc/header.php';
?>

<h1>Edit About Section</h1>

<?php if ($message): ?><div class="message"><?= $message ?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Current Profile Image</label>
            <?php if ($about['profile_image']): ?>
                <div><img src="../<?= htmlspecialchars($about['profile_image']) ?>" class="current-img"></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Upload New Profile Image (leave empty to keep current)</label>
            <input type="file" name="profile_image" accept="image/*" class="form-control">
        </div>
        
        <div class="form-group">
            <label>CV Link (relative path or full URL)</label>
            <input type="text" name="cv_link" value="<?= htmlspecialchars($about['cv_link']) ?>" placeholder="e.g. cv.pdf or https://example.com/cv.pdf" class="form-control">
        </div>
        
        <button type="submit" class="btn">Update About</button>
    </form>
</div>

<?php require_once 'inc/footer.php'; ?>