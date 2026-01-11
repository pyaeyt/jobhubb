<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/functions.php';
// Use requireEmployer() to allow both admins and employers,
// we will handle specific permissions inside the page.
requireEmployer();

// Pass employer ID to get stats for a specific employer, or null for admin (all stats)
$employer_id = isAdmin() ? null : $_SESSION['user_id'];
$stats = getDashboardStats($employer_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JobHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f5f7fb; }
        .sidebar {
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            min-height: 100vh;
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        .main-content { margin-left: 250px; padding: 2rem; }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .content-card, .page-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            padding: 2rem;
        }
        .page-header { margin-bottom: 2rem; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 border-bottom">
            <h4 class="text-primary">
                <i class="fas fa-briefcase me-2"></i>JobHub Admin
            </h4>
        </div>
        
        <nav class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?p=users">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?p=jobs">
                        <i class="fas fa-briefcase me-2"></i>Jobs
                    </a>
                </li>
                 <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?p=companies">
                        <i class="fas fa-building me-2"></i>Companies
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?p=applications">
                        <i class="fas fa-file-alt me-2"></i>Applications
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-2"></i>Back to Site
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <span class="text-muted">
                Welcome,
                <?php
                    if ($_SESSION['user_role'] === 'admin') {
                        echo '<span class="text-danger">Admin</span>';
                    } elseif ($_SESSION['user_role'] === 'employer') {
                        echo '<span class="text-primary">Employee</span>';
                    } elseif ($_SESSION['user_role'] === 'user') {
                        echo '<span class="text-success">Job Seeker</span>';
                    } else {
                        echo htmlspecialchars($_SESSION['user_role']);
                    }
                    echo ' (' . htmlspecialchars($_SESSION['full_name']) . ')';
                ?>
            </span>
        </div>
        
        <div class="page-content">
        <?php
            $page = $_GET['p'] ?? 'dashboard';
            
            // Basic router
            switch ($page) {
                case 'companies':
                    include 'companies.php';
                    break;
                case 'company-add':
                    include 'company-form.php';
                    break;
                case 'company-edit':
                    include 'company-form.php';
                    break;
                case 'jobs':
                    include 'jobs.php';
                    break;
                case 'job-add':
                    include 'job-add.php';
                    break;
                case 'job-edit':
                    include 'job-edit.php';
                    break;
                case 'applications':
                    include 'applications.php';
                    break;
                case 'application-view':
                    include 'application-view.php';
                    break;
                case 'users':
                    include 'users.php';
                    break;
                case 'user-edit':
                    include 'user-edit.php';
                    break;
                case 'dashboard':
                default:
                    include 'dashboard-main.php';
                    break;
            }
        ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 