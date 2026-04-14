<?php
require_once __DIR__ . "/../../auth.php";
$user = require_role(["patient"]);
$role = $user["role"];
$active = "payments";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Patient - Payment History</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . "/../../partials/sidebar.php"; ?>
<main class="main">
  <div class="pageHead"><h1 class="pageHead__title">Payment History</h1></div>
  <div class="card" style="background:#e9f7ff;"><b>Coming soon:</b> invoices + payment receipts.</div>
</main>
</body>
</html>