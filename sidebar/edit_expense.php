<?php

session_start();

$conn = require '../database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_page.html");
    exit;
}

$expense_id = $_GET['id'];
$user_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("SELECT * FROM expense WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$expense = $result->fetch_assoc();

$stmt2 = $conn->prepare("SELECT name, profile_image FROM customer WHERE user_id = ?");
    $stmt2->bind_param("i", $_SESSION['customer_id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $customer = $result2->fetch_assoc();
?>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SmartSpend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>
  <link rel="stylesheet" href="styledash.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row vh-100">
      <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary shadow-lg" style="width: 280px;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
          <span class="fs-4">SmartSpend</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item">
            <a href="main-page.php" class="nav-link link-body-emphasis m-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-clipboard-data-fill" viewBox="0 0 16 16">
                <path
                  d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z" />
                <path
                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5zM10 8a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zm-6 4a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm4-3a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1" />
              </svg>
              Dashboard
            </a>
          </li>
          <li>
            <a href="expenses.php" class="nav-link active m-1" aria-current="page">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-credit-card-fill" viewBox="0 0 16 16">
                <path
                  d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1" />
              </svg>
              Expense Tracker
            </a>
          </li>
          <li>
            <a href="cur-converter.php" class="nav-link link-body-emphasis m-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-currency-exchange" viewBox="0 0 16 16">
                <path
                  d="M0 5a5 5 0 0 0 4.027 4.905 6.5 6.5 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05q-.001-.07.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.5 3.5 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98q-.004.07-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5m16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0m-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674z" />
              </svg>
              Currency Converter
            </a>
          </li>
        </ul>
        <hr>
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="../uploads/profile_images/<?= htmlspecialchars($customer['profile_image']) ?>" 
              alt="Profile" width="32" height="32" class="rounded-circle me-2">
            <strong><?= htmlspecialchars($customer['name']) ?></strong>
          </a>
          <ul class="dropdown-menu text-small shadow">
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
          </ul>
        </div>
      </div>
    

    <!-- Main Content -->
    <div class="container-expenses col-7 m-4  p-4 ps-3 container">
      <h2>Edit Expense</h2>
<form class="card p-4 shadow-sm" action="update_expense.php" method="post">
  
  <!-- Hidden input for expense ID -->
  <input type="hidden" name="id" value="<?= htmlspecialchars($expense['id']) ?>">

  <div class="mb-3">
    <label for="expenseName" class="form-label">Expense Name</label>
    <input type="text" class="form-control" id="expenseName" name="expense_name"
           value="<?= htmlspecialchars($expense['expense_name']) ?>" required>
  </div>

  <div class="mb-3">
    <label for="expenseAmount" class="form-label">Amount</label>
    <input type="number" class="form-control" id="expenseAmount" name="expense_amount"
           value="<?= htmlspecialchars($expense['amount']) ?>" min="0" step="0.01" required>
  </div>

  <div class="mb-3">
    <label for="expenseCategory" class="form-label">Category</label>
    <select class="form-select" id="expenseCategory" name="expense_category" required>
      <option value="">Select category</option>
      <option value="Food" <?= $expense['category'] === 'Food' ? 'selected' : '' ?>>Food</option>
      <option value="Transport" <?= $expense['category'] === 'Transport' ? 'selected' : '' ?>>Transport</option>
      <option value="Utilities" <?= $expense['category'] === 'Utilities' ? 'selected' : '' ?>>Utilities</option>
      <option value="Entertainment" <?= $expense['category'] === 'Entertainment' ? 'selected' : '' ?>>Entertainment</option>
      <option value="Other" <?= $expense['category'] === 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="expenseDate" class="form-label">Date</label>
    <input type="date" class="form-control" id="expenseDate" name="expense_date"
           value="<?= htmlspecialchars($expense['date']) ?>" required>
  </div>

  <button type="submit" class="btn btn-primary">Edit Expense</button>
</form>