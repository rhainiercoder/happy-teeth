<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$active = $active ?? ""; // e.g. "home", "about", "services", "testimonials", "contact"
?>
<header class="topnav">
  <div class="topnav__inner">
    <a class="topnav__brand" href="/happy-teeth/index.php">
      <span class="topnav__brandTitle">Happy Teeth</span>
      <span class="topnav__brandSub">Dental Clinic</span>
    </a>

    <nav class="topnav__links">
      <a class="topnav__link <?php echo $active==="home" ? "is-active":""; ?>" href="/happy-teeth/index.php">Home</a>
      <a class="topnav__link <?php echo $active==="about" ? "is-active":""; ?>" href="/happy-teeth/about.php">About Us</a>
      <a class="topnav__link <?php echo $active==="services" ? "is-active":""; ?>" href="/happy-teeth/services.php">Services</a>
      <a class="topnav__link <?php echo $active==="testimonials" ? "is-active":""; ?>" href="/happy-teeth/testimonials.php">Testimonials</a>
      <a class="topnav__link <?php echo $active==="contact" ? "is-active":""; ?>" href="/happy-teeth/contact.php">Contact</a>
    </nav>

    <div class="topnav__actions">
      <?php if (!empty($_SESSION["user_id"])): ?>
        <a class="topnav__btn topnav__btn--ghost" href="/happy-teeth/dashboard.php">Dashboard</a>
        <a class="topnav__btn topnav__btn--primary" href="/happy-teeth/logout.php">Logout</a>
      <?php else: ?>
        <a class="topnav__btn topnav__btn--ghost" href="/happy-teeth/login.php">Login</a>
        <a class="topnav__btn topnav__btn--primary" href="/happy-teeth/signup.php">Sign up</a>
      <?php endif; ?>
    </div>
  </div>
</header>