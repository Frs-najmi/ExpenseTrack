<?php
session_start();

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $email = $mysqli->real_escape_string($_POST["email"]);
    $password = $_POST["password"];
    
    // 1. Check in Admin table 
    $sql = "SELECT * FROM admin WHERE email = '$email'";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id();
        $_SESSION["admin_id"] = $user["admin_id"];
        $_SESSION["role"] = "admin";  
        header("Location: admin_dashboard.php"); 
        exit;
    }
    
    // 2. Check in Customer table
    $sql = "SELECT * FROM customer WHERE email = '$email'";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id();
        $_SESSION["customer_id"] = $user["user_id"];
        $_SESSION["role"] = "customer"; 
        header("Location: sidebar/main-page.php");
        exit;
    }
    
    // 3. If no match found
    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div id="logo">SmartSpend</div>

   <?php if ($is_invalid): ?>
        <em>Invalid login</em>
    <?php endif; ?>

  <div class="login-container">
    <form method="post">
      <div class="input-group">
        <div class="input-box">
          <label for="email">Email</label>
          <input type="email" name="email" id="email"
               value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        </div>

        <div class="input-box">
          <label for="password">Password</label>
          <input type="password" name="password" id="password">
        </div>
      </div>

      <button type="submit">Log In</button>
    </form>


    <p>Don't have an account? <a href="register-page.html">Create an account</a></p>
  </div>
</body>

</html>