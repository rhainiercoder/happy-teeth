<?php
require_once __DIR__ . "/../../auth.php";
$user = require_role(["patient"]);
$role = $user["role"];
$active = "location";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Patient - Location & Map</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . "/../../partials/sidebar.php"; ?>
<main class="main">
  <div class="pageHead"><h1 class="pageHead__title">Location &amp; Map</h1></div>
  <div class="card" style="background:#e9f7ff;">
    <b>Coming soon:</b> clinic map + directions.
    <div class="map" style="margin-top:12px;">
      <iframe
        title="Clinic Location Map"
        class="map__frame"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        src="https://www.google.com/maps?q=Manila%2C%20Philippines&output=embed"
      ></iframe>
    </div>
  </div>
</main>
</body>
</html>