<?php
if (!isset($_GET['id']) || !($application = getApplicationById((int)$_GET['id']))) {
    // Redirect or show a proper error message
    header("Location: index.php?p=applications");
    exit();
}
?>
<a href="index.php?p=applications" class="btn btn-outline-secondary mb-3">
    <i class="fas fa-arrow-left me-2"></i>Back to Applications
</a>
<h1 class="h3 mb-4">Application Details</h1>

<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <div class="alert alert-success">Status updated successfully!</div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'locked'): ?>
    <div class="alert alert-danger">This application has already been accepted or rejected and cannot be changed.</div>
<?php endif; ?>

<div class="content-card p-4">
    <div class="application-details">
        <div class="row">
            <div class="col-md-6">
                <p><span class="label">Applicant:</span> <?php echo htmlspecialchars($application['applicant_name']); ?></p>
                <p><span class="label">Email:</span> <a href="mailto:<?php echo htmlspecialchars($application['applicant_email']); ?>"><?php echo htmlspecialchars($application['applicant_email']); ?></a></p>
            </div>
            <div class="col-md-6">
                <p><span class="label">Project:</span> <?php echo htmlspecialchars($application['job_title']); ?></p>
                <p><span class="label">Company:</span> <?php echo htmlspecialchars($application['company_name']); ?></p>
            </div>
        </div>
         <p><span class="label">Applied on:</span> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($application['date_applied']))); ?></p>
        <p>
            <span class="label">Status:</span>
            <span class="badge bg-<?php echo getApplicationStatusBadgeClass($application['status']); ?>">
                <?php echo htmlspecialchars(ucfirst($application['status'])); ?>
            </span>
        </p>
         <p>
            <span class="label">Resume:</span>
            <?php if (!empty($application['resume_path']) && file_exists('../' . $application['resume_path'])): ?>
                <a href="../<?php echo htmlspecialchars($application['resume_path']); ?>" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fas fa-download me-2"></i>Download Resume
                </a>
            <?php else: ?>
                <span class="text-muted">No resume uploaded.</span>
            <?php endif; ?>
        </p>
        <hr>
        <h5 class="mt-4">Cover Letter</h5>
        <div class="cover-letter" style="background-color: #fafafa; border-left: 4px solid #0d6efd; padding: 1.5rem; margin-top: 1rem; white-space: pre-wrap;">
            <p><?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?></p>
        </div>
    </div>
</div>

<?php if (!in_array($application['status'], ['accepted', 'rejected'])): ?>
<div class="content-card p-4 mt-4">
    <h5 class="mb-3">Update Application Status</h5>
    <form id="statusUpdateForm" action="application-update-status.php" method="POST">
        <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
        <div class="input-group">
            <select name="status" class="form-select" id="statusSelect">
                <option value="pending" <?php echo $application['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="accepted" <?php echo $application['status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo $application['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
    </form>
</div> 
<script>
document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
    var status = document.getElementById('statusSelect').value;
    if (status === 'accepted') {
        if (!confirm('Are you sure you want to accept this application?')) {
            e.preventDefault();
        }
    } else if (status === 'rejected') {
        if (!confirm('Are you sure you want to reject this application?')) {
            e.preventDefault();
        }
    }
});
</script>
<?php endif; ?> 