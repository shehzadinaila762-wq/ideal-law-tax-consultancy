<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="site-header">
  <div class="header-container">

    <a href="index.php" class="logo">
      <span class="logo-icon">⚖️</span>
      <span class="logo-text">IDEAL LAW & TAX CONSULTANCY</span>
    </a>

    <button class="mobile-menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <nav class="main-nav">
      <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
        Home
      </a>

      <a href="services.php" class="<?= $currentPage === 'services.php' ? 'active' : '' ?>">
        Services
      </a>

      <a href="about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">
        About
      </a>

      <a href="gallery.php" class="<?= $currentPage === 'gallery.php' ? 'active' : '' ?>">
        Gallery
      </a>

      <a href="team.php" class="<?= $currentPage === 'team.php' ? 'active' : '' ?>">
        Our Team
      </a>

      <a href="contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">
        Contact
      </a>

      <a href="admin.php" class="<?= $currentPage === 'admin.php' ? 'active' : '' ?>">
        Admin Panel
      </a>
    </nav>

  </div>
</header>

<script>
  (function () {
    var toggle = document.querySelector('.mobile-menu-toggle');
    var nav = document.querySelector('.main-nav');

    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  })();
</script>
