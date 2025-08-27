<?php
session_start();
$conn = require 'database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

$stmt = $conn->prepare("SELECT user_id, name, email, profile_image FROM customer");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - SmartSpend</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: white;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .header-bar h3 {
      margin: 0;
      font-weight: bold;
    }
    .card-container {
      padding: 2rem;
      max-width: 1200px;
      margin: auto;
    }
    .profile-pic {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 0.5rem;
      border-top-right-radius: 0.5rem;
    }
    .card {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      border: none;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <div class="header-bar">
    <h3>SmartSpend</h3>
    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="card-container">
    <h2 class="mb-4">Customer List</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100">
            <img src="uploads/profile_images/<?= htmlspecialchars($row['profile_image']) ?>" 
              alt="Profile Picture" class="profile-pic" 
              onerror="this.onerror=null; this.src='uploads/d3a65316-5c72-479f-bb03-f98cf49f64be.png';">

            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
              <p class="card-text"><strong>ID:</strong> <?= $row['user_id'] ?></p>
              <form action="delete_customer.php" method="post" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                <input type="hidden" name="id" value="<?= $row['user_id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

</body>
</html>
