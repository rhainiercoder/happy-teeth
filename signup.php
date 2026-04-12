<?php
session_start();
require __DIR__ . "/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "" || $confirm === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = "Email is already registered. Please login.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "patient";

            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hash, $role);
            $stmt->execute();

            $success = "Account created! You can now login.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Sign Up - Happy Teeth</title>
</head>
<body>
  <h1>Patient Sign Up</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
  <?php endif; ?>

  <form method="post" action="signup.php" autocomplete="off">
    <label>Full Name</label><br />
    <input type="text" name="name" required /><br /><br />

    <label>Email</label><br />
    <input type="email" name="email" required /><br /><br />

    <label>Password</label><br />
    <input type="password" name="password" required /><br /><br />

    <label>Confirm Password</label><br />
    <input type="password" name="confirm_password" required /><br /><br />

    <button type="submit">Create Account</button>
  </form>

  <p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>