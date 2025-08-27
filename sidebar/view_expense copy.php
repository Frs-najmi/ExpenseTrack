<?php
session_start();

$conn = require '../database.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login_page.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Fetch expenses for this user
$stmt = $conn->prepare("SELECT id, expense_name, amount, category, date FROM expense WHERE customer_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();


$stmt2 = $conn->prepare("SELECT name, profile_image FROM customer WHERE user_id = ?");
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$customer_result = $stmt2->get_result();
$customer = $customer_result->fetch_assoc();

?>

<!DOCTYPE html>
<html>
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
    <title>My Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body class="container-fluid">
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
          <li class="mb-1"> <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0"
              data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="true">
              Expense Tracker
            </button>
            <div class="collapse show" id="dashboard-collapse" >
              <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="expenses.php" class="link-body-emphasis d-inline-flex rounded text-decoration-none ">Add Expenses</a></li>
                <li><a href="view_expense copy.php" class=" d-inline-flex text-decoration-none rounded nav-link active" aria-current="page">View Expenses</a></li>
                </ul>
            </div>
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
        
      <div class ="container col-9">
        <h2 class="mb-4 ">My Expenses</h2>

        <div class="mb-3">
          <input type="text" id="searchInput" class="form-control" placeholder="Search expenses by name, category, or date...">
        </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table.striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="col-2">Name</th>
                    <th class="col-1">Amount (RM)</th>
                    <th class="col-1">Category</th>
                    <th class="col-1">Date</th>
                    <th class="col-1">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['expense_name']) ?></td>
                    <td><?= number_format($row['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td>
                        <a href="edit_expense.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_expense.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this expense?')" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No expenses recorded yet.</div>
    <?php endif; ?>
      </div>
        
    </div>

    <script>
  document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
</script>


</body>
</html>
