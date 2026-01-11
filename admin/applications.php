<?php
// For employers, filter applications by their user_id. Admins see all.
$employer_id = isAdmin() ? null : $_SESSION['user_id'];
$applications = getAllApplications($employer_id);
?>
<h1 class="h3 mb-4">Job Applications</h1>
<div class="content-card p-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover" id="applicationsTable">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Project Title</th>
                    <th>Company</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($app['applicant_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($app['job_title'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($app['company_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($app['date_applied']))); ?></td>
                        <td>
                            <span class="badge bg-<?php echo getApplicationStatusBadgeClass($app['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?p=application-view&id=<?php echo $app['application_id']; ?>" class="btn btn-info btn-sm">View</a>
                            <?php if (isAdmin()): ?>
                            <a href="delete.php?type=application&id=<?php echo $app['application_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this application?');">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.dataTable.isDataTable('#applicationsTable')) {
            $('#applicationsTable').DataTable().destroy();
        }
        $('#applicationsTable').DataTable({
            "pageLength": 10
        });
    });
</script>
