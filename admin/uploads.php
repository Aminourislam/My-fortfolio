<?php
$uploadDir = '../uploads/'; 
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['file']['tmp_name'];
    $name = basename($_FILES['file']['name']);
    $targetPath = $uploadDir . time() . '_' . $name;
    
    if (move_uploaded_file($tmpName, $targetPath)) {
        echo json_encode(['location' => '/' . $targetPath]); // URL path to the image
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save image']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Upload error']);
}
?>