<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/functions.php';
requireEmployer();

// For employers, filter jobs by their user_id. Admins see all jobs.
$filters = [];
if (!isAdmin()) {
    $filters['employer_id'] = $_SESSION['user_id'];
}

// Admins can see all jobs, active or inactive. Employers see all of theirs.
$jobs = getAllJobs(null, 0, $filters, true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-header">Jobs</h1>
    <a href="index.php?p=job-edit" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Job
    </a>
</div>

<?php 
if (isset($_GET['success'])):
    $message = '';
    if ($_GET['success'] === 'create') {
        $message = 'Project added successfully!';
    } elseif ($_GET['success'] === 'update') {
        $message = 'Project updated successfully!';
    } elseif ($_GET['success'] === 'delete') {
        $message = 'Project deleted successfully!';
    }
?>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover" id="jobsTable">
        <thead>
            <tr>
                <th>Title</th>
                <th>Company</th>
                <th>Applications</th>
                <th>Status</th>
                <th>Created On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['title'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($job['company_name'] ?? ''); ?></td>
                    <td>
                        <?php echo $job['application_count']; ?>
                        <a href="index.php?p=applications&job_id=<?php echo $job['id']; ?>" class="btn btn-outline-info btn-sm ms-2">View</a>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo getStatusBadgeClass($job['status']); ?>">
                            <?php echo ucfirst($job['status']); ?>
                        </span>
                    </td>
                    <td><?php echo formatDate($job['created_at']); ?></td>
                    <td>
                        <a href="index.php?p=job-edit&id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="delete.php?type=job&id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add DataTables CSS and JS to the main template if not already present -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Prevent re-initialization
        if (!$.fn.DataTable.isDataTable('#jobsTable')) {
            $('#jobsTable').DataTable();
        }
    });
</script>