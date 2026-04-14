<?php
// expects: $role (string)
if (!isset($role) || $role === "") {
    http_response_code(500);
    die("Sidebar role not set.");
}

$active = $active ?? ""; // "dashboard", "patients", "appointments", etc.
function active_class(string $key, string $active): string {
    return $key === $active ? " is-active" : "";
}
?>
<aside class="sidebar">
  <div class="sidebar__brand">
    <div class="sidebar__logo"><img src="/happy-teeth/assets/img/logo.png" alt="Happy Teeth" class="sidebarLogo"></div>
  </div>

  <nav class="sidebar__nav">
    <?php if ($role === "admin" || $role === "staff"): ?>
      <a class="sidebar__link<?php echo active_class("dashboard", $active); ?>" href="/happy-teeth/dashboards/admin.php">Dashboard</a>
      <a class="sidebar__link<?php echo active_class("patients", $active); ?>" href="/happy-teeth/pages/admin/patients.php">Patients</a>
      <a class="sidebar__link<?php echo active_class("dentists", $active); ?>" href="/happy-teeth/pages/admin/dentists.php">Dentist</a>
      <a class="sidebar__link<?php echo active_class("appointments", $active); ?>" href="/happy-teeth/pages/admin/appointments.php">Appointments</a>
      <a class="sidebar__link<?php echo active_class("records", $active); ?>" href="/happy-teeth/pages/admin/dental-records.php">Dental Records</a>
      <a class="sidebar__link<?php echo active_class("transactions", $active); ?>" href="/happy-teeth/pages/admin/transactions.php">Transactions</a>
      <a class="sidebar__link<?php echo active_class("reports", $active); ?>" href="/happy-teeth/pages/admin/reports.php">Reports</a>
      <a class="sidebar__link<?php echo active_class("location", $active); ?>" href="/happy-teeth/pages/admin/location.php">Location &amp; Map</a>
      <a class="sidebar__link<?php echo active_class("settings", $active); ?>" href="/happy-teeth/pages/admin/settings.php">Settings</a>

    <?php elseif ($role === "dentist"): ?>
      <a class="sidebar__link<?php echo active_class("dashboard", $active); ?>" href="/happy-teeth/dashboards/dentist.php">Dashboard</a>
      <a class="sidebar__link<?php echo active_class("today", $active); ?>" href="/happy-teeth/pages/dentist/today.php">Today's Patient</a>
      <a class="sidebar__link<?php echo active_class("records", $active); ?>" href="/happy-teeth/pages/dentist/dental-records.php">Dental Records</a>
      <a class="sidebar__link<?php echo active_class("transactions", $active); ?>" href="/happy-teeth/pages/dentist/transactions.php">Transaction History</a>
      <a class="sidebar__link<?php echo active_class("location", $active); ?>" href="/happy-teeth/pages/dentist/location.php">Location &amp; Map</a>
      <a class="sidebar__link<?php echo active_class("settings", $active); ?>" href="/happy-teeth/pages/dentist/settings.php">Settings</a>

    <?php elseif ($role === "patient"): ?>
      <a class="sidebar__link<?php echo active_class("dashboard", $active); ?>" href="/happy-teeth/dashboards/patient.php">Dashboard</a>
      <a class="sidebar__link<?php echo active_class("appointments", $active); ?>" href="/happy-teeth/pages/patient/appointments.php">My Appointments</a>
      <a class="sidebar__link<?php echo active_class("records", $active); ?>" href="/happy-teeth/pages/patient/dental-records.php">My Dental Record</a>
      <a class="sidebar__link<?php echo active_class("payments", $active); ?>" href="/happy-teeth/pages/patient/payments.php">Payment History</a>
      <a class="sidebar__link<?php echo active_class("location", $active); ?>" href="/happy-teeth/pages/patient/location.php">Location &amp; Map</a>
      <a class="sidebar__link<?php echo active_class("settings", $active); ?>" href="/happy-teeth/pages/patient/settings.php">Settings</a>

    <?php else: ?>
      <?php http_response_code(403); die("Forbidden"); ?>
    <?php endif; ?>
  </nav>

  <div class="sidebar__footer">
    <a class="sidebar__logout" href="/happy-teeth/logout.php">Log out</a>
  </div>
</aside>