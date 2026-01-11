<section id="content">
    <div class="container">
        <h2>My Applications</h2>
        <hr>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Your application has been submitted successfully!</div>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
            <p>You have not submitted any applications yet.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Company</th>
                        <th>Date Applied</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><a href="job-details.php?id=<?php echo $app['job_id']; ?>"><?php echo htmlspecialchars($app['job_title']); ?></a></td>
                        <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                        <td><?php echo formatDateTime($app['applied_at']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo getApplicationStatusBadgeClass($app['status']); ?>">
                                <?php echo ucfirst($app['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section> 