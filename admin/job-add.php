<?php
// Get all companies for the dropdown
$companies = getAllCompanies();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $required_fields = [
        'company_id' => 'Company',
        'title' => 'Job Title',
        'description' => 'Job Description',
        'requirements' => 'Requirements',
        'location' => 'Location',
        'type' => 'Job Type',
        'experience_level' => 'Experience Level'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[] = "$label is required.";
        }
    }

    if (empty($errors)) {
        try {
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'requirements' => trim($_POST['requirements']),
                'company_id' => $_POST['company_id'],
                'employer_id' => $_SESSION['user_id'],
                'location' => trim($_POST['location']),
                'type' => $_POST['type'],
                'salary_min' => !empty($_POST['salary_min']) ? $_POST['salary_min'] : null,
                'salary_max' => !empty($_POST['salary_max']) ? $_POST['salary_max'] : null,
                'experience_level' => $_POST['experience_level']
            ];
            
            if (createJob($data)) {
                header("Location: index.php?p=jobs&success=1");
                exit();
            } else {
                $errors[] = "Failed to create job. Please try again.";
            }
        } catch (Exception $e) {
            $errors[] = "Error creating job: " . $e->getMessage();
        }
    }
}
?>

<div class="container-fluid">
    <h1 class="page-header">Add New Project</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?p=job-add" method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="company_id">Company</label>
                    <select class="form-control" id="company_id" name="company_id" required>
                        <option value="">Select Company</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="e.g., San Francisco, CA" required>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="title">Job Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Senior PHP Developer" required>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="type">Job Type</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="one-month">one month</option>
                        <option value="two-month">two month</option>
                        <option value="three-month">three month</option>
                        
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="experience_level">Experience Level</label>
                    <select class="form-control" id="experience_level" name="experience_level" required>
                        <option value="">Select Level</option>
                        <option value="entry">Entry Level</option>
                        <option value="mid">Mid Level</option>
                        <option value="senior">Senior Level</option>
                        <option value="executive">Executive Level</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="salary_min">Minimum Salary ($)</label>
                    <input type="number" class="form-control" id="salary_min" name="salary_min" placeholder="e.g., 50000">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="salary_max">Maximum Salary ($)</label>
            <input type="number" class="form-control" id="salary_max" name="salary_max" placeholder="e.g., 80000">
        </div>

        <div class="form-group mb-3">
            <label for="requirements">Requirements</label>
            <textarea class="form-control" id="requirements" name="requirements" rows="4" placeholder="List the required skills and qualifications..." required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="description">Job Description</label>
            <textarea class="form-control" id="description" name="description" rows="6" placeholder="Provide a detailed description of the job responsibilities..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Project</button>
        <a href="index.php?p=jobs" class="btn btn-default">Cancel</a>
    </form>
</div> 