<?php
session_start();
$conn = require '../database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login_page.php");
    exit;
}

$expense_id = $_POST['id'];
$name = $_POST['expense_name'];
$amount = $_POST['expense_amount'];
$category = $_POST['expense_category'];
$date = $_POST['expense_date'];
$user_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("UPDATE expense SET expense_name = ?, amount = ?, category = ?, date = ? WHERE id = ? AND customer_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sdssii", $name, $amount, $category, $date, $expense_id, $user_id);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Expense updated successfully!'); window.location.href='view_expense copy.php';</script>";
} else {
    echo "<script>alert('No changes made or invalid ID.'); window.location.href='view_expense copy.php';</script>";
}
?>
