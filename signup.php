<?php
session_start();
require __DIR__ . "/db.php";

$error = "";
$success = "";

// If already logged in, redirect
if (!empty($_SESSION["user"]["role"])) {
    $role = $_SESSION["user"]["role"];
    if ($role === "admin" || $role === "staff") {
        header("Location: /happy-teeth/dashboards/admin.php");
    } elseif ($role === "dentist") {
        header("Location: /happy-teeth/dashboards/dentist.php");
    } else {
        header("Location: /happy-teeth/dashboards/patient.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $role = "patient";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Name, email, and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = "Email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hash, $role);
            $stmt->execute();

            $success = "Account created! You can now log in.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Sign up - Happy Teeth</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/base.css">
  <link rel="stylesheet" href="/happy-teeth/assets/css/public.css">
  <link rel="stylesheet" href="/happy-teeth/assets/css/auth.css">
</head>
<body class="authPage">

  <?php require __DIR__ . "/partials/public_header.php"; ?>

  <div class="authShell">
    <section class="authHero">
      <img class="authHero__img" src="/happy-teeth/assets/img/facility.jpg" alt="Happy Teeth clinic">
      <div class="authHero__overlay"></div>

      <div class="authQuote">
        A healthy smile begins with a simple appointment.
        <span class="authQuote__by">Happy Teeth Dental Clinic</span>
      </div>
    </section>

    <section class="authPanel">
      <h1 class="authH1">Create account</h1>
      <p class="authLead">Sign up to book an appointment.</p>

      <?php if (!empty($success)): ?>
        <div class="authMsg2 authMsg2--ok"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="authMsg2 authMsg2--error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="authDivider">Details</div>

      <form method="post">
        <div class="authField">
          <span class="authField__icon">👤</span>
          <input name="name" placeholder="Full name" required>
        </div>

        <div class="authField">
          <span class="authField__icon">✉</span>
          <input name="email" type="email" placeholder="Email" required>
        </div>

        <div class="authField">
          <span class="authField__icon">🔒</span>
          <input name="password" type="password" placeholder="Password" required>
        </div>

        <button class="authSubmit" type="submit">Sign up</button>

        <div class="authBottomText">
          Already have an account? <a href="/happy-teeth/login.php">Log in</a>
        </div>
      </form>
    </section>
  </div>

</body>
</html>