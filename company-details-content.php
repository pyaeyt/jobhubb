<?php
// company-details-content.php
?>
<div class="container" style="padding: 60px 0; max-width: 900px;">
  <div class="company-details-header" style="display: flex; align-items: center; gap: 30px; margin-bottom: 40px;">
    <div class="company-logo-large" style="width: 100px; height: 100px; background: #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
      <?php if (!empty($company['logo'])): ?>
        <img src="<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['name']); ?>" style="max-width: 90px; max-height: 90px; object-fit: contain;">
      <?php else: ?>
        <span style="font-size: 2.5rem; color: #718096; font-weight: 700;">
          <?php echo strtoupper(substr($company['name'], 0, 2)); ?>
        </span>
      <?php endif; ?>
    </div>
    <div>
      <h1 style="margin: 0 0 10px 0; font-size: 2.2rem; color: #2d3748; font-weight: 800;">
        <?php echo htmlspecialchars($company['name']); ?>
      </h1>
      <div style="color: #4a5568; font-size: 1.1rem; margin-bottom: 8px;">
        <?php if (!empty($company['industry'])): ?>
          <span style="margin-right: 18px;"><i class="fa fa-briefcase"></i> <?php echo htmlspecialchars($company['industry']); ?></span>
        <?php endif; ?>
        <?php if (!empty($company['location'])): ?>
          <span><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($company['location']); ?></span>
        <?php endif; ?>
      </div>
      <?php if (!empty($company['website'])): ?>
        <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" style="color: #425569; font-weight: 600; text-decoration: none;">
          <i class="fa fa-globe"></i> <?php echo htmlspecialchars($company['website']); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($company['description'])): ?>
    <div style="margin-bottom: 35px; color: #4a5568; font-size: 1.1rem; line-height: 1.7;">
      <?php echo nl2br(htmlspecialchars($company['description'])); ?>
    </div>
  <?php endif; ?>
  <h2 style="font-size: 1.5rem; color: #2d3748; font-weight: 700; margin-bottom: 20px;">Open Jobs at <?php echo htmlspecialchars($company['name']); ?></h2>
  <?php if (empty($jobs)): ?>
    <div style="color: #718096; font-size: 1.1rem; margin-bottom: 30px;">No active job openings at the moment.</div>
    <a href="jobs.php" class="btn btn-primary-modern" style="background: #425569; color: #fff; border-radius: 50px; padding: 12px 30px; font-weight: 600;">Browse All Jobs</a>
  <?php else: ?>
    <div class="jobs-grid" style="display: grid; grid-template-columns: 1fr; gap: 25px;">
      <?php foreach ($jobs as $job): ?>
        <div class="job-card-modern" style="background: #fff; border: 1px solid #e9ecef; border-radius: 12px; padding: 25px; display: flex; flex-direction: column;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 1.2rem; color: #1a252f; font-weight: 700;">
              <a href="job-details.php?id=<?php echo $job['id']; ?>" style="color: #1a252f; text-decoration: none;"> <?php echo htmlspecialchars($job['title']); ?> </a>
            </h3>
            <span class="job-type-badge <?php echo $job['type']; ?>" style="padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background-color: #e9ecef; color: #6c757d;">
              <?php echo ucfirst(str_replace('-', ' ', $job['type'])); ?>
            </span>
          </div>
          <div style="color: #6c757d; font-size: 1rem; margin-bottom: 10px;">
            <?php if (!empty($job['location'])): ?>
              <span style="margin-right: 15px;"><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($job['location']); ?></span>
            <?php endif; ?>
            <?php if (!empty($job['salary_min']) || !empty($job['salary_max'])): ?>
              <span><i class="fa fa-money"></i> <?php echo formatSalary($job['salary_min'], $job['salary_max']); ?></span>
            <?php endif; ?>
          </div>
          <div style="color: #4a5568; font-size: 1rem; margin-bottom: 10px;">
            <?php echo htmlspecialchars(substr($job['description'], 0, 100)); ?><?php if (strlen($job['description']) > 100) echo '...'; ?>
          </div>
          <a href="job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary-modern" style="align-self: flex-end; background: #425569; color: #fff; border-radius: 50px; padding: 8px 22px; font-weight: 600; text-decoration: none;">View Job</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div> 