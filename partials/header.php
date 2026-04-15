<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$active = $active ?? ""; // e.g. "home", "about", "services", "testimonials", "contact"
<<<<<<< HEAD
=======

// In this project, login.php sets $_SESSION["user"] (array).
$isLoggedIn = !empty($_SESSION["user"]) && !empty($_SESSION["user"]["id"]);
>>>>>>> 1aabe8a3aaf38697cdd3494a24653af7cf05663b
?>
<header class="topnav">
  <div class="topnav__inner">
    <a class="topnav__brand" href="/happy-teeth/index.php">
      <span class="topnav__brandTitle">Happy Teeth</span>
      <span class="topnav__brandSub">Dental Clinic</span>
    </a>

    <nav class="topnav__links">
<<<<<<< HEAD
      <a class="topnav__link <?php echo $active==="home" ? "is-active":""; ?>" href="/happy-teeth/index.php">Home</a>
      <a class="topnav__link <?php echo $active==="about" ? "is-active":""; ?>" href="/happy-teeth/about.php">About Us</a>
      <a class="topnav__link <?php echo $active==="services" ? "is-active":""; ?>" href="/happy-teeth/services.php">Services</a>
      <a class="topnav__link <?php echo $active==="testimonials" ? "is-active":""; ?>" href="/happy-teeth/testimonials.php">Testimonials</a>
      <a class="topnav__link <?php echo $active==="contact" ? "is-active":""; ?>" href="/happy-teeth/contact.php">Contact</a>
    </nav>

    <div class="topnav__actions">
      <?php if (!empty($_SESSION["user_id"])): ?>
        <a class="topnav__btn topnav__btn--ghost" href="/happy-teeth/dashboard.php">Dashboard</a>
=======
      <a class="topnav__link <?php echo $active==="home" ? "is-active":""; ?>" href="/happy-teeth/index.php#home">Home</a>
      <a class="topnav__link <?php echo $active==="about" ? "is-active":""; ?>" href="/happy-teeth/index.php#about">About Us</a>
      <a class="topnav__link <?php echo $active==="services" ? "is-active":""; ?>" href="/happy-teeth/index.php#services">Services</a>
      <a class="topnav__link <?php echo $active==="testimonials" ? "is-active":""; ?>" href="/happy-teeth/index.php#testimonials">Testimonials</a>
      <a class="topnav__link <?php echo $active==="contact" ? "is-active":""; ?>" href="/happy-teeth/index.php#contacts">Contact</a>
    </nav>

    <div class="topnav__actions">
      <?php if ($isLoggedIn): ?>
        <a class="topnav__btn topnav__btn--ghost" href="/happy-teeth/dashboards/patient.php">Dashboard</a>
>>>>>>> 1aabe8a3aaf38697cdd3494a24653af7cf05663b
        <a class="topnav__btn topnav__btn--primary" href="/happy-teeth/logout.php">Logout</a>
      <?php else: ?>
        <a class="topnav__btn topnav__btn--ghost" href="/happy-teeth/login.php">Login</a>
        <a class="topnav__btn topnav__btn--primary" href="/happy-teeth/signup.php">Sign up</a>
      <?php endif; ?>
    </div>
  </div>
<<<<<<< HEAD
</header>
=======
</header>
>>>>>>> 1aabe8a3aaf38697cdd3494a24653af7cf05663b
