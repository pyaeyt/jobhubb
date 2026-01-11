<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>JOBHUB / <?php echo isset($title) ? $title : 'Home'; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="Find your dream job with JobHub - The leading job portal" />
<meta name="author" content="JobHub" />
<!-- css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css?family=Noto+Serif:400,400italic,700|Open+Sans:400,600,700" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.2/flexslider.min.css" />
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="theme/css/modern.css" rel="stylesheet">

<style type="text/css">
/* Keeping only essential, non-conflicting styles if any. Or removing all. */
/* For now, we will remove all the old styles as they are in modern.css */
.footer-main {
    background: #f4f8fd !important;
    color: #232946;
    border-top: 3px solid #e0e7ef;
    box-shadow: 0 -2px 16px rgba(36,56,99,0.07);
    padding: 48px 0 24px 0;
    font-family: 'Montserrat', 'Open Sans', Arial, sans-serif;
}
.footer-main h4 {
    color: #232946;
    font-weight: 800;
    margin-bottom: 1em;
}
.footer-main p, .footer-main li, .footer-main .stat-label {
    color: #6B7280;
    font-size: 1.05em;
}
.footer-main a, .footer-main .social-links a {
    color: #3b82f6 !important;
    transition: color 0.2s;
}
.footer-main a:hover, .footer-main .social-links a:hover {
    color: #232946 !important;
}
.footer-main .social-links a {
    background: #fff;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-size: 1.2em;
    box-shadow: 0 2px 8px rgba(36,56,99,0.07);
}
.footer-main .social-links a:hover {
    background: #3b82f6;
    color: #fff !important;
}
/* Modern Glassmorphism Navbar */
.modern-glass-navbar {
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
  background: rgba(255,255,255,0.75);
  backdrop-filter: blur(16px);
  box-shadow: 0 4px 24px rgba(36,56,99,0.10);
  padding: 0.7em 0;
  font-family: 'Montserrat', 'Open Sans', Arial, sans-serif;
}
.container.nav-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2em;
}
.navbar-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}
.logo-circle {
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(36,56,99,0.10);
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.logo-circle img {
  width: 28px;
  height: 28px;
}
.brand-name {
  font-weight: 900;
  font-size: 1.5em;
  color: #3b82f6;
  letter-spacing: -1px;
  font-family: 'Montserrat',sans-serif;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 1.5em;
  list-style: none;
  margin: 0;
  padding: 0;
}
.nav-links li a {
  color: #232946;
  font-weight: 600;
  font-size: 1.08em;
  padding: 0.7em 1.1em;
  border-radius: 2em;
  text-decoration: none;
  transition: color 0.2s, background 0.2s;
  position: relative;
}
.nav-links li.active a,
.nav-links li a:hover,
.nav-links li a:focus {
  color: #3b82f6;
  background: rgba(59,130,246,0.08);
}
.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.7em;
}
.btn.nav-btn {
  font-weight: 700;
  border-radius: 2em;
  padding: 0.5em 1.5em;
  font-size: 1.08em;
  transition: background 0.2s, color 0.2s;
  box-shadow: none;
}
.btn-outline-primary.nav-btn {
  background: transparent;
  color: #3b82f6;
  border: 2px solid #3b82f6;
}
.btn-outline-primary.nav-btn:hover {
  background: #3b82f6;
  color: #fff;
}
.btn-primary.nav-btn {
  background: #3b82f6;
  color: #fff;
  border: 2px solid #3b82f6;
}
.btn-primary.nav-btn:hover {
  background: #232946;
  color: #fff;
  border-color: #232946;
}
.user-link {
  color: #232946;
  font-weight: 700;
  text-decoration: none;
}
.user-link:hover {
  color: #3b82f6;
}
/* Hamburger for mobile */
.nav-toggle {
  display: none;
}
.nav-toggle-label {
  display: none;
  flex-direction: column;
  cursor: pointer;
  gap: 4px;
  margin-left: 1em;
}
.nav-toggle-label span {
  width: 28px;
  height: 3px;
  background: #3b82f6;
  border-radius: 2px;
  display: block;
}
@media (max-width: 900px) {
  .container.nav-flex {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5em;
  }
  .nav-links {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5em;
    margin-top: 1em;
    display: none;
  }
  .nav-actions {
    margin-top: 1em;
    justify-content: flex-start;
  }
  .nav-toggle {
    display: block;
    position: absolute;
    right: 1.5em;
    top: 1.5em;
    width: 32px;
    height: 32px;
    opacity: 0;
    z-index: 2;
  }
  .nav-toggle-label {
    display: flex;
    position: absolute;
    right: 1.5em;
    top: 1.5em;
    z-index: 3;
  }
  .nav-toggle:checked ~ .nav-links {
    display: flex;
    background: rgba(255,255,255,0.95);
    box-shadow: 0 8px 32px rgba(36,56,99,0.13);
    border-radius: 1em;
    padding: 1em;
    position: absolute;
    left: 0;
    right: 0;
    top: 60px;
    z-index: 10;
  }
}
</style>
</head>
<body>
<div id="wrapper">
	<!-- start header -->
	<header>
  <nav class="modern-glass-navbar">
    <div class="container nav-flex">
      <a class="navbar-logo" href="index.php">
        <span class="logo-circle">
          <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4bc.svg" alt="JobHub Logo">
        </span>
        <span class="brand-name">JobHub</span>
      </a>
      <input type="checkbox" id="nav-toggle" class="nav-toggle">
      <label for="nav-toggle" class="nav-toggle-label">
        <span></span>
        <span></span>
        <span></span>
      </label>
      <ul class="nav-links">
        <li class="<?php echo isset($active_home) ? 'active' : '';?>"><a href="index.php">Home</a></li>
        <li class="<?php echo isset($active_about) ? 'active' : '';?>"><a href="about.php">About Us</a></li>
        <li class="<?php echo isset($active_jobs) ? 'active' : '';?>"><a href="jobs.php">Jobs</a></li>
        <li class="<?php echo isset($active_companies) ? 'active' : '';?>"><a href="companies.php">Companies</a></li>
        <li class="<?php echo isset($active_contact) ? 'active' : '';?>"><a href="contact.php">Contact</a></li>
      </ul>
      <div class="nav-actions">
        <?php if(isLoggedIn()): ?>
          <div class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle user-link">
              <i class="fa fa-user"></i> <?php echo $_SESSION['full_name']; ?> <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <?php if(isAdmin() || isEmployer()): ?>
                <li><a href="admin/index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <?php endif; ?>
              <li><a href="applications.php"><i class="fa fa-files-o"></i> My Applications</a></li>
              <li class="divider"></li>
              <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="register.php" class="btn btn-outline-primary nav-btn">Register</a>
          <a href="login.php" class="btn btn-primary nav-btn">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
	</header>
	<!-- end header -->

    <?php
        if (isset($content) && !empty($content)) {
            require_once($content);
        } else {
            require_once('home.php');
        }
    ?>

	<footer>
		<div class="footer-main">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-sm-6">
						<div class="footer-section">
							<h4>About JobHub</h4>
							<p>JobHub is the leading job portal connecting talented professionals with innovative companies. We're committed to making job searching and hiring simple, efficient, and successful.</p>
							<div class="social-links">
								<a href="#"><i class="fa fa-facebook"></i></a>
								<a href="#"><i class="fa fa-twitter"></i></a>
								<a href="#"><i class="fa fa-linkedin"></i></a>
								<a href="#"><i class="fa fa-instagram"></i></a>
								<a href="#"><i class="fa fa-youtube"></i></a>
							</div>
						</div>
					</div>
					<div class="col-md-2 col-sm-6">
						<div class="footer-section">
							<h4>Quick Links</h4>
							<ul>
								<li><a href="index.php">Home</a></li>
								<li><a href="about.php">About Us</a></li>
								<li><a href="jobs.php">Find Jobs</a></li>
								<li><a href="companies.php">Companies</a></li>
								<li><a href="contact.php">Contact</a></li>
								<li><a href="register.php">Register</a></li>
							</ul>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="footer-section">
							<h4>For Employers</h4>
							<ul>
								<li><a href="admin/index.php">Post a Job</a></li>
								<li><a href="admin/index.php">Dashboard</a></li>
								
							</ul>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="footer-section">
							<h4>Contact Info</h4>
							<ul class="contact-info">
								<li><i class="fa fa-map-marker"></i>Yan Myo Naing 4th Street<br>Magway,Myanmar</li>
								<li><i class="fa fa-phone"></i> +959 763005004</li>
								<li><i class="fa fa-envelope"></i> info@jobhub.com</li>
								<li><i class="fa fa-clock-o"></i> Mon - Fri: 9:00 AM - 6:00 PM</li>
							</ul>
							<div class="newsletter-form">
								<input type="email" placeholder="Subscribe to newsletter">
								<button type="submit"><i class="fa fa-paper-plane"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<p class="copyright">&copy; 2025 JobHub. All rights reserved.</p>
					</div>
					<div class="col-md-6 text-right">
						<div class="footer-links">
							<a href="#">Privacy Policy</a>
							<a href="#">Terms of Service</a>
							<a href="#">Cookie Policy</a>
							<a href="#">Sitemap</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>
<a href="#" class="scrollup"><i class="fa fa-angle-up active"></i></a>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.2/jquery.flexslider-min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });
</script>
<script>
// Navbar Login Form AJAX Submission
$(document).ready(function() {
    $('#navbar-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var messageDiv = $('#navbar-login-message');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                messageDiv.html('<div class="alert alert-danger">An error occurred.</div>');
            }
        });
    });
});
</script>
<script>
// Sticky Navbar
$(window).on('scroll', function() {
    if ($(window).scrollTop() > 50) {
        $('header .navbar').addClass('navbar-scrolled');
    } else {
        $('header .navbar').removeClass('navbar-scrolled');
    }
});
</script>
<script>
// Stats Counter
$(document).ready(function() {
  function counter(event) {
    var $this = $(this),
    countTo = $this.attr('data-count');
    
    $({ countNum: $this.text()}).animate({
      countNum: countTo
    },
    {
      duration: 2000,
      easing:'linear',
      step: function() {
        $this.text(Math.floor(this.countNum));
      },
      complete: function() {
        $this.text(this.countNum);
      }
    });  
  }
  
  var fired = false;
  $(window).on('scroll', function() {
    if (fired === false) {
      var hT = $('.stats-section').offset().top,
          hH = $('.stats-section').outerHeight(),
          wH = $(window).height(),
          wS = $(this).scrollTop();
      if (wS > (hT+hH-wH)){
        $('.stat-card h3').each(counter);
        fired = true;
      }
    }
  });
});
</script>

</body>
</html>
