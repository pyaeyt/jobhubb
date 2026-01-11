<div class="page-header-section">
    <div class="container" data-aos="fade-up">
        <h1>Find Your Next Job</h1>
        <p>Search through hundreds of available projects to find your perfect match.</p>
    </div>
</div>

<div class="job-listings-section section-padding">
    <div class="container">
        <div class="row">
            <!-- Search Filters -->
            <div class="col-lg-12">
                <div class="search-filters" data-aos="fade-up">
                    <form method="GET" action="jobs.php">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Project title, keyword...">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="location" class="form-control" placeholder="Location">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-control">
                                <option value="">Select Type</option>
                                     <option value="full-time">full time</option>
                                     <option value="part time">part time </option>
                                     <option value="contract">contract</option>
                                     <option value="internship">intership</option>
                        
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Job Listings -->
            <div class="col-lg-12" style="margin-top: 40px;">
                <?php if (empty($jobs)): ?>
                    <div class="text-center" data-aos="fade-up">
                        <h3>No jobs found.</h3>
                        <p>Try adjusting your search filters.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): 
                        $logo = $job['company_logo'];
                        if (empty($logo)) {
                            $svg = '<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="10" fill="#aaa">' . htmlspecialchars(substr($job['company_name'], 0, 1)) . '</text></svg>';
                            $logo = 'data:image/svg+xml;base64,' . base64_encode($svg);
                        }
                    ?>
                    <div class="job-listing-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="job-listing-logo">
                            <img src="<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
                        </div>
                        <div class="job-listing-details">
                            <h4><a href="job-details.php?id=<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></h4>
                            <p class="job-listing-info">
                                <span><i class="fa fa-building-o"></i> <?php echo htmlspecialchars($job['company_name']); ?></span>
                                <span><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            </p>
                        </div>
                        <div class="job-listing-actions">
                            <a href="job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                            <p class="posted-date">Posted on: <?php echo formatDate($job['created_at']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 