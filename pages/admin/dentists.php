<?php
require_once __DIR__ . "/../../auth.php";
$user = require_role(["admin","staff"]);
$role = $user["role"];
$active = "dentists";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin - Dentists</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . "/../../partials/sidebar.php"; ?>
<main class="main">
  <div class="pageHead"><h1 class="pageHead__title">Dentists</h1></div>
  <div class="card" style="background:#e9f7ff;"><b>Coming soon:</b> dentist list/schedules/availability.</div>
</main>
</body>
</html>