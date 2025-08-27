<?php
session_start();

$conn = require 'database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_page.php");
    exit;
}

if (isset($_POST['id'])) {
    $customer_id = $_POST['id'];

    //delete all related expenses (to maintain foreign key constraints)
    $stmt1 = $conn->prepare("DELETE FROM expense WHERE customer_id = ?");
    $stmt1->bind_param("i", $customer_id);
    $stmt1->execute();

    //delete the customer
    $stmt2 = $conn->prepare("DELETE FROM customer WHERE user_id = ?");
    $stmt2->bind_param("i", $customer_id);
    $stmt2->execute();
}

header("Location: admin_dashboard.php");
exit;
