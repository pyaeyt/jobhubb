<section id="company-listing">
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <h1>Our Partner Companies</h1>
      <p class="subtitle">Discover organizations working with top professionals through our platform</p>
    </div>

   
    
    <!-- Company Grid -->
    <div class="company-grid">
      <?php foreach ($companies as $company): ?>
        <div class="company-card">
          <div class="company-logo">
            <?php if (!empty($company['logo'])): ?>
              <img src="<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['name']); ?>">
            <?php else: ?>
              <div class="logo-placeholder">
                <?php echo strtoupper(substr($company['name'], 0, 2)); ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="company-info">
            <h3><?php echo htmlspecialchars($company['name']); ?></h3>
            <div class="company-meta">
              <?php if (!empty($company['industry'])): ?>
                <span class="industry"><?php echo htmlspecialchars($company['industry']); ?></span>
              <?php endif; ?>
              <?php if (!empty($company['location'])): ?>
                <span class="location"><?php echo htmlspecialchars($company['location']); ?></span>
              <?php endif; ?>
            </div>
            <div class="company-description">
              <?php 
                $description = !empty($company['description']) ? $company['description'] : 'A leading company in their industry.';
                echo htmlspecialchars(substr($description, 0, 120)) . (strlen($description) > 120 ? '...' : '');
              ?>
            </div>
            <div class="company-footer">
              <?php 
                $jobs = getJobsByCompany($company['id']);
                $jobCount = count($jobs);
              ?>
              <span class="project-count"><?php echo $jobCount; ?> active job<?php echo $jobCount != 1 ? 's' : ''; ?></span>
              <a href="company-details.php?id=<?php echo $company['id']; ?>" class="view-link">View</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['totalPages'] > 1): ?>
    <div class="pagination">
      <?php $current = $pagination['page']; $total = $pagination['totalPages']; ?>
      <a href="?page=<?php echo $current-1; ?>" class="page-nav<?php if ($current <= 1) echo ' disabled'; ?>" <?php if ($current <= 1) echo 'tabindex="-1" aria-disabled="true"'; ?>>Previous</a>
      <?php
        $range = 2; // how many pages to show around current
        $start = max(1, $current - $range);
        $end = min($total, $current + $range);
        if ($start > 1) {
          echo '<a href="?page=1" class="page-number">1</a>';
          if ($start > 2) echo '<span class="page-ellipsis">...</span>';
        }
        for ($i = $start; $i <= $end; $i++) {
          echo '<a href="?page=' . $i . '" class="page-number' . ($i == $current ? ' active' : '') . '">' . $i . '</a>';
        }
        if ($end < $total) {
          if ($end < $total - 1) echo '<span class="page-ellipsis">...</span>';
          echo '<a href="?page=' . $total . '" class="page-number">' . $total . '</a>';
        }
      ?>
      <a href="?page=<?php echo $current+1; ?>" class="page-nav<?php if ($current >= $total) echo ' disabled'; ?>" <?php if ($current >= $total) echo 'tabindex="-1" aria-disabled="true"'; ?>>Next</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<style>
/* Modern Gray Color Scheme */
:root {
  --dark-gray: #232946;
  --medium-gray: #6B7280;
  --light-gray: #e2e8f0;
  --lighter-gray: #f4f8fd;
  --accent-gray: #3b82f6;
  --border-gray: #cbd5e0;
  --text-color: #232946;
}

/* Base Styles */
#company-listing {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: var(--text-color);
  padding: 60px 0;
  background-color: var(--lighter-gray);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Page Header */
.page-header {
  text-align: center;
  margin-bottom: 50px;
  background: linear-gradient(120deg, #363b54, #7ea2e6, #3b82f6, #232946);
  background-size: 300% 300%;
  animation: heroGradient 12s ease-in-out infinite;
  padding: 40px 0 30px 0;
  border-radius: 12px;
}

.page-header h1,
.page-header .subtitle {
  color: #fff;
}

.page-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 15px;
}

.subtitle {
  font-size: 1.1rem;
  color: var(--medium-gray);
  max-width: 600px;
  margin: 0 auto;
}

/* Stats Bar */
.stats-bar {
  display: flex;
  justify-content: space-between;
  margin-bottom: 50px;
  flex-wrap: wrap;
  gap: 20px;
}

.stat-item {
  flex: 1;
  min-width: 200px;
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  text-align: center;
}

.stat-number {
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--dark-gray);
  margin-bottom: 5px;
}

.stat-label {
  font-size: 1rem;
  color: var(--medium-gray);
}

/* Company Grid */
.company-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 25px;
  margin-bottom: 50px;
}

.company-card {
   
  background: #fff;
  border-radius: 1.2em;
  box-shadow: 0 4px 24px rgba(36,56,99,0.07);
  transition: box-shadow 0.2s, transform 0.2s;
}

.company-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 32px rgba(59,130,246,0.13);
}

.company-logo {
  padding: 30px;
  text-align: center;
  border-bottom: 1px solid var(--light-gray);
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f4f8fd;
}

.company-logo img {
  max-height: 60px;
  max-width: 180px;
  object-fit: contain;
}

.logo-placeholder {
  width: 60px;
  height: 60px;
  background: var(--light-gray);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.5rem;
  color: var(--medium-gray);
}

.company-info {
  padding: 25px;
}

.company-info h3 {
  font-size: 1.3rem;
  margin-bottom: 15px;
  font-weight: 600;
  color: var(--dark-gray);
}

.company-meta {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  font-size: 0.9rem;
}

.industry, .location {
  display: flex;
  align-items: center;
}

.industry:before {
  content: "•";
  margin-right: 5px;
  color: var(--accent-gray);
}

.company-description {
  color: var(--medium-gray);
  line-height: 1.5;
  margin-bottom: 20px;
  font-size: 0.95rem;
}

.company-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid var(--light-gray);
}

.project-count {
  font-size: 0.9rem;
  color: var(--accent-gray);
}

.view-link {
  color: var(--accent-gray);
  font-weight: 500;
  text-decoration: none;
  border-bottom: 1.5px solid var(--accent-gray);
  padding-bottom: 2px;
  transition: color 0.2s ease;
}

.view-link:hover {
  color: #232946;
  text-decoration: none;
  border-bottom: 1.5px solid #232946;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-top: 50px;
}

.page-nav, .page-number {
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  color: var(--medium-gray);
  font-weight: 500;
  transition: all 0.2s ease;
}

.page-nav {
  background-color: var(#f8f9fa);
}

.page-number {
  min-width: 40px;
  text-align: center;
}

.page-number:hover:not(.active) {
  background-color: var(--light-gray);
}

.page-number.active {
  background-color: var(--dark-gray);
  color: white;
}

.page-ellipsis {
  padding: 8px 5px;
}

.page-nav.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .company-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header h1 {
    font-size: 2rem;
  }
  
  .stat-item {
    min-width: 100%;
  }
}
@keyframes heroGradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>