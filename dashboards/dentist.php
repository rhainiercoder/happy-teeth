<?php
require_once __DIR__ . "/../auth.php";
$user = require_role(["dentist"]);
$role = $user["role"];
$active = "dashboard";

require_once __DIR__ . "/../db.php";
function h($v){ return htmlspecialchars((string)$v); }

$today = date("Y-m-d");

// Real "Today's Patient" list for this dentist
$stmt = $conn->prepare("
  SELECT
    a.id,
    u.name AS patient_name,
    a.appointment_time,
    s.name AS service
  FROM appointments a
  JOIN users u ON u.id = a.patient_id
  JOIN services s ON s.id = a.service_id
  WHERE a.status = 'approved'
    AND a.appointment_date = ?
    AND a.dentist_id = ?
  ORDER BY a.appointment_time ASC
");
$stmt->bind_param("si", $today, $user["id"]);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Dentist Dashboard</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . "/../partials/sidebar.php"; ?>

  <main class="main">
    <div class="pageHead">
      <h1 class="pageHead__title">
        Welcome to Your Dashboard<br />
        Dr. <?php echo h($user["name"] ?? ""); ?>!
      </h1>

      <div class="profileChip" title="Profile">
        <div class="profileChip__avatar"></div>
        <div class="profileChip__chev">▾</div>
      </div>
    </div>

    <section class="card dentistSection">
      <h2 class="sectionTitle">Today's Patient! (<?php echo h($today); ?>)</h2>

      <div class="table">
        <div class="table__row table__row--head" style="grid-template-columns: 1.2fr .6fr 1fr .7fr;">
          <div>Patient</div>
          <div>Time</div>
          <div style="text-align:right;">Service</div>
          <div style="text-align:right;">Action</div>
        </div>

        <?php foreach ($rows as $r): ?>
          <div class="table__row" style="grid-template-columns: 1.2fr .6fr 1fr .7fr;">
            <div class="patientCell">
              <div class="patientCell__icon">
                <img src="/happy-teeth/assets/img/teeth_icon.png" alt=""
                    style="width:22px;height:22px;object-fit:contain;display:block;">
              </div>
              <div>
                <div class="patientCell__name"><?php echo h($r["patient_name"]); ?></div>
                <div class="patientCell__sub">Patient</div>
              </div>
            </div>

            <div class="table__muted"><?php echo h(substr($r["appointment_time"], 0, 5)); ?></div>
            <div class="table__right"><?php echo h($r["service"]); ?></div>

            <div style="text-align:right;">
              <a class="btn btn--dark"
                 href="/happy-teeth/pages/dentist/dental-records.php?appointment_id=<?php echo (int)$r["id"]; ?>">
                Add Record
              </a>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (!$rows): ?>
          <div class="table__row">
            <div style="grid-column:1 / -1; font-weight:900; opacity:.75;">
              No approved appointments for today.
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="card dentistSection">
      <h2 class="sectionTitle">Your Operating Hours</h2>

      <div class="hours">
        <div class="hours__row">
          <div class="hours__left"><span class="clock">🕘</span> Monday</div>
          <div class="hours__right">10:00 AM – 12:00 PM</div>
        </div>
        <div class="hours__row">
          <div class="hours__left"><span class="clock">🕘</span> Tuesday</div>
          <div class="hours__right">10:00 AM – 12:00 PM</div>
        </div>
        <div class="hours__row">
          <div class="hours__left"><span class="clock">🕘</span> Friday</div>
          <div class="hours__right">1:00 PM – 3:00 PM</div>
        </div>
        <div class="hours__row">
          <div class="hours__left"><span class="clock">🕘</span> Saturday</div>
          <div class="hours__right">1:00 PM – 3:00 PM</div>
        </div>
      </div>
    </section>

    <section class="card dentistSection">
      <h2 class="sectionTitle">Clinic Location</h2>

      <div class="map">
        <iframe
          title="Clinic Location Map"
          class="map__frame"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          src="https://www.google.com/maps?q=Manila%2C%20Philippines&output=embed"
        ></iframe>
      </div>
    </section>
  </main>
</body>
</html>