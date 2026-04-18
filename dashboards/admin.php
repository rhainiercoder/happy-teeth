<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

$user = require_role(["admin", "staff"]);
$role = $user["role"];
$active = "dashboard";

function h($v){ return htmlspecialchars((string)$v); }

/* Helper to read settings (k/v) */
function get_setting($conn, $k, $default = '') {
    $s = $conn->prepare("SELECT v FROM settings WHERE k = ? LIMIT 1");
    if ($s) {
        $s->bind_param("s", $k);
        $s->execute();
        $r = $s->get_result()->fetch_assoc();
        return $r['v'] ?? $default;
    }
    return $default;
}

/* ---------- Stats ---------- */
$today = date("Y-m-d");

// Today's appointments (approved + pending for today)
$todayAppointments = 0;
try {
    $stmt = $conn->prepare("
      SELECT COUNT(*) AS c
      FROM appointments
      WHERE appointment_date = ?
        AND status IN ('approved','pending')
    ");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $todayAppointments = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
} catch (Exception $e) {
    // appointments table might not exist yet — keep default 0
}

// Pending approvals (all pending)
$pendingApprovals = 0;
try {
    $res = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status = 'pending'");
    $pendingApprovals = (int)($res->fetch_assoc()['c'] ?? 0);
} catch (Exception $e) {
    // ignore
}

// Available dentists (count users with role = dentist)
// --- Clinic timezone & clinic "day" (rollover at 08:00) ---
$clinicDate = null;
try {
    // Attempt to read clinic_day_start and timezone from settings (fallback to hardcoded)
    $clinic_day_start = get_setting($conn, 'clinic_day_start', '08:00');
    $clinic_timezone = get_setting($conn, 'clinic_timezone', 'Asia/Manila');
    // If you have a helper get_clinic_date, use it; otherwise compute using timezone and day start.
    if (function_exists('get_clinic_date')) {
        $clinicDate = get_clinic_date($clinic_day_start, $clinic_timezone);
    } else {
        // Basic fallback: treat clinic day as server date (not considering rollover)
        $clinicDate = date("Y-m-d");
    }
} catch (Exception $e) {
    $clinicDate = date("Y-m-d");
}
$clinicWeekday = (int)date('N', strtotime($clinicDate)); // 1=Mon .. 7=Sun

// --- Option A: Available dentists (scheduled at any time today) ---
$availableDentists = 0;
try {
    // If you have a holidays table, treat holidays as zero available
    $hCount = 0;
    try {
        $stmtH = $conn->prepare("SELECT COUNT(*) AS c FROM holidays WHERE date = ? LIMIT 1");
        $stmtH->bind_param("s", $clinicDate);
        $stmtH->execute();
        $hCount = (int)($stmtH->get_result()->fetch_assoc()['c'] ?? 0);
    } catch (Exception $e) {
        // no holidays table -> ignore
        $hCount = 0;
    }

    if ($hCount > 0) {
        $availableDentists = 0;
    } else {
        // count distinct dentists who have availability rows for this weekday
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT dentist_id) AS c FROM dentist_availability WHERE `day` = ?");
        $stmt->bind_param("i", $clinicWeekday);
        $stmt->execute();
        $availableDentists = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);

        // Fallback: if availability table missing or returned 0, show total dentists (backwards compatible)
        if ($availableDentists === 0) {
            $res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'dentist'");
            $availableDentists = (int)($res->fetch_assoc()['c'] ?? 0);
        }
    }
} catch (Exception $e) {
    // generic fallback on any error
    $res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'dentist'");
    $availableDentists = (int)($res->fetch_assoc()['c'] ?? 0);
}

// Clinic working hours: try a settings table fallback, otherwise default
$clinicHours = "9:00 – 18:00";
try {
    $clinicHoursSetting = get_setting($conn, 'clinic_hours', '');
    if (!empty($clinicHoursSetting)) $clinicHours = $clinicHoursSetting;
} catch (Exception $e) {
    // settings table may not exist — keep default
}

/* ---------- Dentist schedule (support ?ym=YYYY-M navigation) ---------- */
// Use requested month/year if present, otherwise current month
if (!empty($_GET['ym']) && preg_match('/^\d{4}-\d{1,2}$/', $_GET['ym'])) {
    list($year, $month) = array_map('intval', explode('-', $_GET['ym']));
    if ($month < 1 || $month > 12) {
        $year = (int)date('Y'); $month = (int)date('n');
    }
} else {
    $year  = (int)date('Y');
    $month = (int)date('n');
}
$firstOfMonth = new DateTime(sprintf('%04d-%02d-01', $year, $month));
$monthDays = (int)$firstOfMonth->format('t'); // days in month

// prev / next month values for links
$prevDt = (clone $firstOfMonth)->modify('-1 month');
$nextDt = (clone $firstOfMonth)->modify('+1 month');
$prev_ym = $prevDt->format('Y-n'); // non-zero-padded month
$next_ym = $nextDt->format('Y-n');

// precompute availability counts per weekday (1=Mon..7=Sun)
$availByWeekday = array_fill(1,7,0);
try {
    $res = $conn->query("SELECT `day`, COUNT(DISTINCT dentist_id) AS c FROM dentist_availability GROUP BY `day`");
    while ($r = $res->fetch_assoc()) {
        $d = (int)$r['day'];
        $availByWeekday[$d] = (int)$r['c'];
    }
} catch (Exception $e) {
    // table may not exist -> keep zeros
}

// Optional holidays override (dates that should be shown as holiday)
$holidays = [];
try {
    $res = $conn->query("SELECT date FROM holidays WHERE YEAR(date) = " . (int)$year . " AND MONTH(date) = " . (int)$month);
    while ($r = $res->fetch_assoc()) {
        $holidays[$r['date']] = true;
    }
} catch (Exception $e) {
    // no holidays table — ignore
}

/* ---------- Recent transactions (latest 6) ---------- */
$recentTransactions = [];
try {
    $stmt = $conn->prepare("
      SELECT t.id, t.amount, t.type, t.status, t.note, t.created_at, u.name AS user_name
      FROM transactions t
      LEFT JOIN users u ON u.id = t.user_id
      ORDER BY t.created_at DESC
      LIMIT 6
    ");
    $stmt->execute();
    $recentTransactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // transactions table may not exist — leave empty
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/qm/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . "/../partials/sidebar.php"; ?>

  <main class="main">
    <div class="pageHead">
      <h1 class="pageHead__title">Welcome Back Admin <?php echo h($user["name"] ?? ""); ?>!</h1>
    </div>



    <!-- Top stats -->
    <section class="grid grid-2 adminStats">
      <div class="card statCard">
        <div class="statCard__icon">📋</div>
        <div class="statCard__label">Today's Appointments</div>
        <div class="statCard__value"><?php echo h($todayAppointments); ?></div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">⏳</div>
        <div class="statCard__label">Pending Approvals</div>
        <div class="statCard__value"><?php echo h($pendingApprovals); ?></div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">🦷</div>
        <div class="statCard__label">Available Dentists</div>
        <div class="statCard__value"><?php echo h($availableDentists); ?></div>
      </div>

      <div class="card statCard">
        <div class="statCard__icon">🕘</div>
        <div class="statCard__label">Clinic Working Hours</div>
        <div class="statCard__value statCard__value--small"><?php echo h($clinicHours); ?></div>
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
          <a class="calendar__nav" href="?ym=<?php echo h($prev_ym); ?>" aria-label="Previous month">‹</a>
          <div class="calendar__month"><?php echo h($firstOfMonth->format('F Y')); ?></div>
          <a class="calendar__nav" href="?ym=<?php echo h($next_ym); ?>" aria-label="Next month">›</a>
        </div>

        <div class="calendar__dow">
          <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <div class="calendar__grid">
          <?php
          // build cells Sun..Sat for the month (include leading blanks)
          $startWeekday = (int)$firstOfMonth->format('w'); // 0 (Sun) .. 6 (Sat)
          $cells = [];
          for ($i=0; $i < $startWeekday; $i++) $cells[] = null;
          for ($d = 1; $d <= $monthDays; $d++) $cells[] = $d;

          foreach ($cells as $cell) {
              if ($cell === null) {
                  echo '<div></div>';
                  continue;
              }
              $dateStr = sprintf("%04d-%02d-%02d", (int)$year, (int)$month, $cell);
              $weekday = (int)date('N', strtotime($dateStr)); // 1..7 Mon..Sun
              $isHoliday = !empty($holidays[$dateStr]);
              $availCount = $availByWeekday[$weekday] ?? 0;
              $cls = $isHoliday ? 'orange' : ($availCount > 0 ? 'green' : 'red');
              echo '<div class="day day--' . $cls . '">' . $cell . '</div>';
          }
          ?>
        </div>

        <div class="calendar__note">Reminder: Arrive 1 Hour Early</div>
      </div>
    </section>

    <!-- Recent Transactions -->
    <section class="card adminTransactions">
      <div class="adminTransactions__head">
        <h2 class="adminTransactions__title">Recent Transactions</h2>
      </div>

      <div class="adminTransactions__box">
        <?php if ($recentTransactions): ?>
          <div style="display:grid; gap:6px;">
            <?php foreach ($recentTransactions as $t): ?>
              <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-weight:800;">
                  <?php echo h($t['user_name'] ?: 'Guest'); ?>
                  <div style="font-weight:700; opacity:.7; font-size:12px;"><?php echo h($t['type']); ?><?php if (!empty($t['note'])) echo " • " . h($t['note']); ?></div>
                </div>
                <div style="text-align:right;">
                  <div style="font-weight:900; color:<?php echo ($t['type'] === 'refund' ? '#e64545' : '#0b2f4f'); ?>;">
                    ₱<?php echo number_format((float)$t['amount'], 2); ?>
                  </div>
                  <div style="font-size:12px; opacity:.75;"><?php echo h(date('Y-m-d H:i', strtotime($t['created_at']))); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div style="padding:14px; font-weight:800; opacity:.8;">No recent transactions.</div>
        <?php endif; ?>
      </div>

      <div class="adminTransactions__actions">
        <a class="btn btn--dark" href="/qm/pages/admin/transactions.php">View All Transactions</a>
      </div>
    </section>
  </main>
</body>
</html>