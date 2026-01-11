<?php
require_once '../includes/functions.php';
requireAdmin();

$companies = getAllCompanies();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-header">Companies</h1>
    <a href="index.php?p=company-add" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Company
    </a>
</div>

<?php 
if (isset($_GET['success'])):
    $message = '';
    if ($_GET['success'] === 'create') {
        $message = 'Company added successfully!';
    } elseif ($_GET['success'] === 'update') {
        $message = 'Company updated successfully!';
    } elseif ($_GET['success'] === 'delete') {
        $message = 'Company deleted successfully!';
    }
?>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover" id="companiesTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(substr($company['description'] ?? '', 0, 50)) . '...'; ?></td>
                    <td>
                        <a href="index.php?p=company-edit&id=<?php echo $company['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete.php?type=company&id=<?php echo $company['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will also delete all associated jobs and applications.');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#companiesTable')) {
            $('#companiesTable').DataTable();
        }
    });
</script> 