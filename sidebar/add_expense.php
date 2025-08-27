<?php

session_start();

$conn = require '../database.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: ../login_page.php");
    exit;
}


//Get form POST input
$name = $_POST['expense_name'];
$amount = $_POST['expense_amount'];
$category = $_POST['expense_category'];
$date = $_POST['expense_date'];
$user_id = $_SESSION['customer_id'];

//Store the input OR Insert to database
$stmt = $conn->prepare("INSERT INTO expense (expense_name, amount, category, date, customer_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdssi", $name, $amount, $category, $date, $user_id);
$stmt->execute();

echo "<script>alert('Expense added successfully!'); window.location.href='view_expense copy.php';</script>";
exit;
?>