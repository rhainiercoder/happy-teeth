<?php
/**
 * partials/public_header.php
 * Shared public header — include on index.php, login.php, signup.php.
 *
 * Expects optional:
 *   $active  string  one of: "home", "about", "services", "testimonials", "contact"
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$active = $active ?? "";

$isLoggedIn = !empty($_SESSION["user"]["id"]);
$role       = $_SESSION["user"]["role"] ?? "";

// Route dashboard link based on role
if ($role === "admin" || $role === "staff") {
    $dashUrl = "/happy-teeth/dashboards/admin.php";
} elseif ($role === "dentist") {
    $dashUrl = "/happy-teeth/dashboards/dentist.php";
} else {
    $dashUrl = "/happy-teeth/dashboards/patient.php";
}

function topnav_active(string $key, string $active): string {
    return $key === $active ? " is-active" : "";
}
?>
<header class="topnav">
  <div class="topnav__inner">
    <a class="topnav__brand" href="/happy-teeth/index.php">
      <img
        class="topnav__brandImg"
        src="/happy-teeth/assets/img/logo.png"
        alt="Happy Teeth logo"
      />
      <div>
        <span class="topnav__brandTitle">Happy Teeth</span>
        <span class="topnav__brandSub">Dental Clinic</span>
      </div>
    </a>

    <nav class="topnav__links">
      <a class="topnav__link<?php echo topnav_active('home', $active); ?>"
         href="/happy-teeth/index.php#home">Home</a>
      <a class="topnav__link<?php echo topnav_active('about', $active); ?>"
         href="/happy-teeth/index.php#about">About Us</a>
      <a class="topnav__link<?php echo topnav_active('services', $active); ?>"
         href="/happy-teeth/index.php#services">Services</a>
      <a class="topnav__link<?php echo topnav_active('testimonials', $active); ?>"
         href="/happy-teeth/index.php#testimonials">Testimonials</a>
      <a class="topnav__link<?php echo topnav_active('contact', $active); ?>"
         href="/happy-teeth/index.php#contacts">Contact</a>
    </nav>

    <div class="topnav__actions">
      <?php if ($isLoggedIn): ?>
        <a class="topnav__btn topnav__btn--ghost"
           href="<?php echo htmlspecialchars($dashUrl); ?>">Dashboard</a>
        <a class="topnav__btn topnav__btn--primary"
           href="/happy-teeth/logout.php">Logout</a>
      <?php else: ?>
        <a class="topnav__btn topnav__btn--ghost"
           href="/happy-teeth/login.php">Login</a>
        <a class="topnav__btn topnav__btn--primary"
           href="/happy-teeth/signup.php">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</header>
