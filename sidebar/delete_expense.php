<?php
session_start();

$conn = require '../database.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: ../login_page.php");
    exit;
}

$expense_id = $_GET['id'];
$user_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("DELETE FROM expense WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();

echo "<script>alert('Expense deleted successfully!'); window.location.href='view_expense copy.php';</script>";
?>