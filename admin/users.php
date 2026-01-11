<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/functions.php';
requireAdmin();

$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - JobHub Admin</title>
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
        }
        .main-content { margin-left: 100px; padding: 1rem; }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
          
        }
    </style>
</head>
<body>
    <!-- Sidebar -->


    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Users</h2>
            <a href="user-edit.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add New User</a>
        </div>

        <?php 
        if (isset($_GET['success'])):
            $message = '';
            if ($_GET['success'] === 'create') {
                $message = 'User added successfully!';
            } elseif ($_GET['success'] === 'update') {
                $message = 'User updated successfully!';
            } elseif ($_GET['success'] === 'delete') {
                $message = 'User deleted successfully!';
            }
        ?>
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="content-card p-3">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge
                                <?php
                                    if ($user['role'] === 'user') {
                                        echo 'bg-success'; // Green for Job Seeker
                                    } elseif ($user['role'] === 'employer') {
                                        echo 'bg-primary'; // Blue for Employee
                                    } elseif ($user['role'] === 'admin') {
                                        echo 'bg-danger'; // Red for Admin
                                    } else {
                                        echo 'bg-info text-dark'; // Default
                                    }
                                ?>
                            ">
                                <?php
                                    if ($user['role'] === 'user') {
                                        echo 'Job Seeker';
                                    } elseif ($user['role'] === 'employer') {
                                        echo 'Employee';
                                    } else {
                                        echo ucfirst($user['role']);
                                    }
                                ?>
                            </span>
                        </td>
                        <td>
                            <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?type=user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 