<!-- Statistics Cards -->
<div class="row mb-4" id="dashboard-stats">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded p-3 me-3">
                    <i class="fas fa-briefcase fa-2x"></i>
                </div>
                <div>
                    <h3 class="mb-1" id="stat-total-jobs"><?php echo $stats['total_jobs']; ?></h3>
                    <p class="text-muted mb-0">Your Active Jobs</p>
                </div>
            </div>
        </div>
    </div>
    <?php if (isAdmin()): ?>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded p-3 me-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div>
                    <h3 class="mb-1" id="stat-total-users"><?php echo $stats['total_users']; ?></h3>
                    <p class="text-muted mb-0">Users</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-warning text-white rounded p-3 me-3">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <div>
                    <h3 class="mb-1" id="stat-total-companies"><?php echo $stats['total_companies'] ?? $stats['total_employers']; ?></h3>
                    <p class="text-muted mb-0">Companies</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-danger text-white rounded p-3 me-3">
                    <i class="fas fa-file-alt fa-2x"></i>
                </div>
                <div>
                    <h3 class="mb-1" id="stat-total-applications"><?php echo $stats['total_applications']; ?></h3>
                    <p class="text-muted mb-0">Applications Received</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-card">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="p-3">
        <div class="row">
            <?php if (isAdmin()): ?>
            <div class="col-md-3">
                <a href="index.php?p=users" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <a href="index.php?p=jobs" class="btn btn-outline-success w-100 mb-2">
                    <i class="fas fa-briefcase me-2"></i>Manage Jobs
                </a>
            </div>
            <?php if (isAdmin()): ?>
            <div class="col-md-3">
                <a href="index.php?p=companies" class="btn btn-outline-warning w-100 mb-2">
                    <i class="fas fa-building me-2"></i>Manage Companies
                </a>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <a href="index.php?p=applications" class="btn btn-outline-danger w-100 mb-2">
                    <i class="fas fa-file-alt me-2"></i>View Applications
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateDashboardStats() {
    $.get('stats-api.php', function(data) {
        // alert(JSON.stringify(data)); // Debug: show the API response (removed)
        if (data && data.success) {
            $('#stat-total-jobs').text(data.total_jobs);
            $('#stat-total-users').text(data.total_users);
            $('#stat-total-companies').text(data.total_companies);
            $('#stat-total-applications').text(data.total_applications);
        }
    });
}
updateDashboardStats(); // Update immediately on page load
setInterval(updateDashboardStats, 10000); // Update every 10 seconds
</script> 