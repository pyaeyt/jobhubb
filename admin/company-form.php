<?php
require_once '../includes/functions.php';
requireAdmin();

$company = [
    'id' => '', 'name' => '', 'description' => '', 'website' => '', 
    'location' => '', 'industry' => '', 'size' => '', 'founded_year' => ''
];
$isEditing = false;

if (isset($_GET['id'])) {
    $isEditing = true;
    $company = getCompanyById((int)$_GET['id']);
    if (!$company) {
        $_SESSION['error_message'] = "Company not found.";
        header('Location: companies.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'website' => $_POST['website'],
        'location' => $_POST['location'],
        'industry' => $_POST['industry'],
        'size' => $_POST['size'],
        'founded_year' => $_POST['founded_year'],
        'logo' => ''
    ];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $data['logo'] = uploadFile($_FILES['logo'], '../uploads/logos/');
    } elseif ($isEditing) {
        $data['logo'] = $company['logo'];
    }

    if ($isEditing) {
        updateCompany($_GET['id'], $data);
        $redirect_url = 'index.php?p=companies&success=update';
    } else {
        createCompany($data);
        $redirect_url = 'index.php?p=companies&success=create';
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
    <title><?php echo $isEditing ? 'Edit' : 'Add'; ?> Company - JobHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fb; }
        .main-content { padding: 1rem; }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2><?php echo $isEditing ? 'Edit' : 'Add New'; ?> Company</h2>
        <div class="content-card p-4 mt-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($company['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($company['description']); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>">
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" class="form-control" value="<?php echo htmlspecialchars($company['industry'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Company Size</label>
                        <input type="text" name="size" class="form-control" value="<?php echo htmlspecialchars($company['size'] ?? ''); ?>">
                    </div>
                     <div class="col-md-3 mb-3">
                        <label class="form-label">Founded Year</label>
                        <input type="number" name="founded_year" class="form-control" value="<?php echo htmlspecialchars($company['founded_year'] ?? ''); ?>">
                    </div>
                </div>
               
                <button type="submit" class="btn btn-primary"><?php echo $isEditing ? 'Update' : 'Create'; ?> Company</button>
                <a href="index.php?p=companies" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html> 