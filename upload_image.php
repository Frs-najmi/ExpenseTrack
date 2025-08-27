<?php
session_start();
$conn = require 'database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_page.php");
    exit;
}

$user_id = $_SESSION['customer_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $file = $_FILES['profile_image'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = 'user_' . $user_id . '.' . $ext;
    $uploadPath = 'images/' . $newName;

    // Move file to uploads folder
    move_uploaded_file($file['tmp_name'], $uploadPath);

    // Save file name to database
    $stmt = $conn->prepare("UPDATE customers SET profile_image = ? WHERE id = ?");
    $stmt->bind_param("si", $newName, $user_id);
    $stmt->execute();

    echo "Upload successful!";
} else {
    echo "Upload failed.";
}
?>
