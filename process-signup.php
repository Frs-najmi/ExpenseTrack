<?php

if (empty($_POST["name"])) {
    die("Name is required");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$role = $_POST["role"];

// Handle image upload
$image_path = null;

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_name = basename($_FILES['profile_image']['name']);
    $target_dir = __DIR__ . "/uploads/profile_images/";
    $target_file = $target_dir . $file_name;

    // Optionally, rename to unique filename
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $unique_name = uniqid() . "." . $ext;
    $target_file = $target_dir . $unique_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!move_uploaded_file($file_tmp, $target_file)) {
        die("Failed to upload image.");
    }

    $image_path = $unique_name;
} else {
    die("Profile image is required.");
}

// Insert into DB
if ($role === "admin") {
    $sql = "INSERT INTO admin (name, email, password_hash, profile_image) VALUES (?, ?, ?, ?)";
} else {
    $sql = "INSERT INTO customer (name, email, password_hash, profile_image) VALUES (?, ?, ?, ?)";
}

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssss",
    $_POST["name"],
    $_POST["email"],
    $password_hash,
    $image_path
);

if ($stmt->execute()) {
    header("Location: signup-success.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        die("Email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
?>
