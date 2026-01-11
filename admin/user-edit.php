<?php
require_once '../includes/functions.php';
requireAdmin();

$user = ['id' => '', 'full_name' => '', 'email' => '', 'role' => 'user'];
$isEditing = false;

if (isset($_GET['id'])) {
    $isEditing = true;
    $user = getUserById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'role' => $_POST['role']
    ];
    $password = $_POST['password'];

    if ($isEditing) {
        updateUser($_GET['id'], $data, $password);
        $redirect_url = 'index.php?p=users&success=update';
    } else {
        registerUser($_POST['email'], $_POST['email'], $password, $_POST['full_name'], $_POST['role']);
        $redirect_url = 'index.php?p=users&success=create';
    }

    header('Location: ' . $redirect_url);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditing ? 'Edit' : 'Add'; ?> User - JobHub Admin</title>
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
        .main-content { margin-left: 250px; padding: 2rem; }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 border-bottom">
            <h4 class="text-primary"><i class="fas fa-briefcase me-2"></i>JobHub Admin</h4>
        </div>
        <nav class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <?php if (isAdmin()): ?>
                <li class="nav-item"><a class="nav-link active" href="users.php"><i class="fas fa-users me-2"></i>Users</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="jobs.php"><i class="fas fa-briefcase me-2"></i>Projects</a></li>
                <?php if (isAdmin()): ?>
                <li class="nav-item"><a class="nav-link" href="companies.php"><i class="fas fa-building me-2"></i>Companies</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="applications.php"><i class="fas fa-file-alt me-2"></i>Applications</a></li>
                <li class="nav-item mt-4"><a class="nav-link" href="../index.php"><i class="fas fa-home me-2"></i>Back to Site</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="main-content">
      
        <h2><?php echo $isEditing ? 'Edit' : 'Add New'; ?> User</h2>
        <div class="content-card p-4 mt-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" <?php if(!$isEditing) echo 'required'; ?>>
                    <?php if($isEditing): ?><small class="form-text text-muted">Leave blank to keep current password.</small><?php endif; ?>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
                        <option value="employer" <?php if($user['role'] == 'employer') echo 'selected'; ?>>Employer</option>
                        <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $isEditing ? 'Update' : 'Create'; ?> User</button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html> 