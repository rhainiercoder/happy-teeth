<?php
session_start();
require __DIR__ . "/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user["password_hash"])) {
            $error = "Invalid email or password.";
        } else {
            // Store minimal user data in session (not the password hash)
            $_SESSION["user"] = [
                "id" => (int)$user["id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "role" => $user["role"],
            ];

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Login - Happy Teeth</title>
</head>
<body>
  <h1>Login</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post" action="login.php" autocomplete="off">
    <label>Email</label><br />
    <input type="email" name="email" required /><br /><br />

    <label>Password</label><br />
    <input type="password" name="password" required /><br /><br />

    <button type="submit">Login</button>
  </form>
  <p>Don’t have an account? <a href="signup.php">Sign up</a></p>
</body>
</html>