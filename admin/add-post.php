<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!-- admin/add-post.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Blog Post</title>
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',          // target the textarea with id="content"
            plugins: 'image link lists',
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
            automatic_uploads: true,
            images_upload_url: 'upload.php', // URL that handles image uploads
            images_upload_handler: function (blobInfo, success, failure) {
                // You can also implement a custom handler here
            }
        });
    </script>
</head>
<body>
    <form action="save-post.php" method="post">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea id="content" name="content"></textarea>
        <button type="submit">Save Post</button>
    </form>
</body>
</html>