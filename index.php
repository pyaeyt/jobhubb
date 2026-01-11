<?php
require_once 'includes/functions.php';

// Get jobs
$jobs = getAllJobs(10, 0, []);

// Get total count
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'");
$stmt->execute();
$totalJobs = $stmt->fetch()['count'];

// Set content and title for template
$content = 'home.php';
$title = 'Home';
$active_home = 'active';

// Include the template
require_once("theme/templates.php");
?>

<body>
    <div class="container"><?php
require_once 'includes/functions.php';

// Get jobs
$jobs = getAllJobs(10, 0, []);

// Get total count
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'");
$stmt->execute();
$totalJobs = $stmt->fetch()['count'];

// Set content and title for template
$content = 'home.php';
$title = 'Home';
$active_home = 'active';

// Include the template
require_once("theme/templates.php");
?>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                   
                    <div class="card-body p-4">

                        <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
                            <div class="alert alert-success">
                                Registration successful! You can now log in.
                            </div>
                        <?php endif; ?>

                        <div id="login-message"></div>

                        <form id="login-form" method="POST">
                            <div class="mb-3">
                                <!-- Existing code -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                   
                    <div class="card-body p-4">

                        <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
                            <div class="alert alert-success">
                                Registration successful! You can now log in.
                            </div>
                        <?php endif; ?>

                        <div id="login-message"></div>

                        <form id="login-form" method="POST">
                            <div class="mb-3">
                                <!-- Existing code -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>