<?php
require_once '../includes/functions.php';
requireEmployer();

$job = [
    'id' => '', 'title' => '', 'description' => '', 'requirements' => '', 
    'company_id' => '', 'employer_id' => $_SESSION['user_id'], 'location' => '', 
    'type' => '', 'salary_min' => '', 'salary_max' => '', 
    'experience_level' => '', 'status' => 'active'
];
$isEditing = false;

if (isAdmin()) {
    $companies = getAllCompanies();
} else {
    $companies = getCompaniesForEmployer($_SESSION['user_id']);
}

if (isset($_GET['id'])) {
    $isEditing = true;
    $job = getJobById((int)$_GET['id']);
    // Security check: ensure employer can only edit their own jobs
    if (!isAdmin() && $job['employer_id'] !== $_SESSION['user_id']) {
        header('Location: jobs.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'requirements' => $_POST['requirements'],
        'company_id' => $_POST['company_id'],
        'employer_id' => $_SESSION['user_id'],
        'location' => $_POST['location'],
        'type' => $_POST['type'],
        'salary_min' => $_POST['salary_min'],
        'salary_max' => $_POST['salary_max'],
        'experience_level' => $_POST['experience_level'],
        'status' => $_POST['status']
    ];

    $redirect_url = 'index.php?p=jobs';
    if ($isEditing) {
        updateJob($_GET['id'], $data);
        $redirect_url .= '&success=update';
    } else {
        createJob($data);
        $redirect_url .= '&success=create';
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
    <title><?php echo $isEditing ? 'Edit' : 'Add'; ?> Project - JobHub Admin</title>
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
        .main-content { margin-left: 100px; padding: 2rem; }
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    

    <div class="main-content">
       
        <h2><?php echo $isEditing ? 'Edit' : 'Add New'; ?> Job</h2>

        <?php if (!isAdmin() && empty($companies)): ?>
            <div class="alert alert-warning mt-4">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>No Company Assigned</h4>
                You are not associated with any company. Please contact an administrator to have a company assigned to you before you can post a project.
            </div>
        <?php else: ?>
            <div class="content-card p-4 mt-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">JOB Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select a Company</option>
                            <?php foreach($companies as $company): ?>
                                <option value="<?php echo $company['id']; ?>" <?php if($job['company_id'] == $company['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($company['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <?php if (isAdmin() && empty($companies)): ?>
                            <small class="form-text text-muted">No companies have been created yet. <a href="company-edit.php">Create one first.</a></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($job['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control" rows="5"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($job['location']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project Type/Duration</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Job Type</option>
                                <option value="full-time" <?php if($job['type'] == 'full-time') echo 'selected'; ?>>Full Time</option>
                                <option value="part-time" <?php if($job['type'] == 'part-time') echo 'selected'; ?>>Part Time</option>
                                <option value="contract" <?php if($job['type'] == 'contract') echo 'selected'; ?>>Contract</option>
                                <option value="internship" <?php if($job['type'] == 'internship') echo 'selected'; ?>>Internship</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Min Salary (Optional)</label>
                            <input type="number" name="salary_min" class="form-control" value="<?php echo htmlspecialchars($job['salary_min']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Salary (Optional)</label>
                            <input type="number" name="salary_max" class="form-control" value="<?php echo htmlspecialchars($job['salary_max']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Experience Level</label>
                             <select name="experience_level" class="form-select">
                                <option value="">Select Experience</option>
                                <option value="entry" <?php if($job['experience_level'] == 'entry') echo 'selected'; ?>>Entry Level</option>
                                <option value="mid" <?php if($job['experience_level'] == 'mid') echo 'selected'; ?>>Mid Level</option>
                                <option value="senior" <?php if($job['experience_level'] == 'senior') echo 'selected'; ?>>Senior Level</option>
                            </select>
                        </div>
                    </div>

                    <?php if(isAdmin()): ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php if($job['status'] == 'active') echo 'selected'; ?>>Active</option>
                            <option value="inactive" <?php if($job['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="status" value="active">
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary" <?php if(!isAdmin() && empty($companies)) echo 'disabled'; ?>>
                        <?php echo $isEditing ? 'Update' : 'Create'; ?> Project
                    </button>
                    <a href="jobs.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 