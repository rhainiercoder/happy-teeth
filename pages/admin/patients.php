<?php
require_once __DIR__ . "/../../auth.php";
$user = require_role(["admin","staff"]);
$role = $user["role"];
$active = "patients";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin - Patients</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/base.css">
  <link rel="stylesheet" href="/happy-teeth/assets/css/dashboard.css">
</head>
<body>
<?php include __DIR__ . "/../../partials/sidebar.php"; ?>
<main class="main">
  <div class="pageHead"><h1 class="pageHead__title">Patients</h1></div>
  <div class="card" style="background:#e9f7ff;"><b>Coming soon:</b> list/search/add/edit patients.</div>
</main>
</body>
</html>