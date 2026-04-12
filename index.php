<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Happy Teeth - Home</title>
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($user['name'] ?? $user['email']); ?>!</h1>
  <p>Role: <strong><?php echo htmlspecialchars($user['role']); ?></strong></p>

  <p><a href="logout.php">Logout</a></p>
</body>
</html>