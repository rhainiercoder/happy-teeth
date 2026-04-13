<?php
require_once __DIR__ . "/../auth.php";
$user = require_role(["admin", "staff"]);
$role = $user["role"];
$active = "dashboard";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/happy-teeth/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . "/../partials/sidebar.php"; ?>

  <main class="main">
    <div class="pageHead">
      <h1 class="pageHead__title">Welcome Back Admin <?php echo htmlspecialchars($user["name"] ?? ""); ?>!</h1>
    </div>

    <!-- Top stats -->
    <section class="grid grid-2 adminStats">
      <div class="card statCard">
        <div class="statCard__icon">📋</div>
        <div class="statCard__label">Today's Appointments</div>
        <div class="statCard__value">14</div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">⏳</div>
        <div class="statCard__label">Pending Approvals</div>
        <div class="statCard__value">4</div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">🦷</div>
        <div class="statCard__label">Available Dentists</div>
        <div class="statCard__value">5</div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">🕘</div>
        <div class="statCard__label">Clinic Working Hours</div>
        <div class="statCard__value statCard__value--small">9:00 – 18:00</div>
      </div>
    </section>

    <!-- Dentist Schedule -->
    <section class="card adminSchedule">
      <div class="adminSchedule__head">
        <h2 class="adminSchedule__title">Dentist Schedule</h2>

        <div class="legend">
          <span class="legend__item"><span class="dot dot--green"></span>On Duty</span>
          <span class="legend__item"><span class="dot dot--red"></span>Off Duty</span>
          <span class="legend__item"><span class="dot dot--orange"></span>Holiday</span>
        </div>
      </div>

      <div class="calendar">
        <div class="calendar__bar">
          <button class="calendar__nav" type="button" aria-label="Previous month">‹</button>
          <div class="calendar__month">February 2026</div>
          <button class="calendar__nav" type="button" aria-label="Next month">›</button>
        </div>

        <div class="calendar__dow">
          <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <div class="calendar__grid">
          <?php
          // Static demo cells (1-28) to match Figma look
          $colors = [
            1=>"red", 2=>"green", 3=>"green", 4=>"red", 5=>"green", 6=>"green", 7=>"green",
            8=>"red", 9=>"green", 10=>"green", 11=>"red", 12=>"green", 13=>"green", 14=>"green",
            15=>"red", 16=>"green", 17=>"orange", 18=>"red", 19=>"orange", 20=>"green", 21=>"green",
            22=>"red", 23=>"green", 24=>"green", 25=>"orange", 26=>"green", 27=>"green", 28=>"green",
          ];
          for ($d=1; $d<=28; $d++):
            $c = $colors[$d] ?? "green";
          ?>
            <div class="day day--<?php echo $c; ?>"><?php echo $d; ?></div>
          <?php endfor; ?>
        </div>

        <div class="calendar__note">Reminder: Arrive 1 Hour Early</div>
      </div>
    </section>

    <!-- Recent Transactions -->
    <section class="card adminTransactions">
      <div class="adminTransactions__head">
        <h2 class="adminTransactions__title">Recent Transactions</h2>
      </div>

      <div class="adminTransactions__box"></div>

      <div class="adminTransactions__actions">
        <a class="btn btn--dark" href="#">View All Transactions</a>
      </div>
    </section>
  </main>
</body>
</html>