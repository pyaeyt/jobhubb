<!-- Hero Section - Modern Design -->

<section class="hero-section-modern">
    <div class="bubble-container">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
    </div>
    <div class="hero-content-modern">
        <h1 class="hero-title">Find Your <span class="highlight">Dream Job</span> Today</h1>
        <p class="hero-subtitle">Join thousands of companies and professionals finding their perfect match</p>
        <form action="jobs.php" method="get" class="search-form-modern">
            <div class="search-input-group">
               
                <input type="text" name="search" class="form-control-modern" placeholder="Job title, keyword, or company">
                <button type="submit" class="btn-primary-modern">
                    <span>Search</span>
                 
                </button>
            </div>
            <div class="quick-tags">
                <span>Popular Searches:</span>
                <a href="jobs.php?search=Developer">Developer</a>
                <a href="jobs.php?search=Designer">Designer</a>
                <a href="jobs.php?search=Marketing">Marketing</a>
                <a href="jobs.php?search=Remote">Remote</a>
            </div>
        </form>
    </div>
</section>

<!-- Featured Jobs Section - Card Grid -->
<section class="section-padding-modern featured-jobs-modern">
    <div class="container">
        <div class="section-header-modern" data-aos="fade-up">
            <h2 class="section-title-modern">Latest Job Openings</h2>
            <a href="jobs.php" class="view-all-link">View All Jobs <i class="fas fa-arrow-right"></i></a>
        </div>
        
        
        
        <div class="jobs-grid">
            <?php
            // Fetch latest 6 jobs
            $latest_jobs = getAllJobs(6);
            $i = 0; // Initialize counter
            foreach ($latest_jobs as $job):
                $i++; // Increment counter
                $typeClass = strtolower(str_replace(' ', '-', $job['type']));
            ?>
            <div class="job-card-modern" data-aos="fade-up" data-aos-delay="<?php echo (($i % 3) * 100) + 100; ?>">
                <div class="job-card-header">
                    <div class="company-logo">
                        <?php if(!empty($job['company_logo'])): ?>
                            <img src="<?php echo htmlspecialchars($job['company_logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
                        <?php else: ?>
                            <div class="logo-placeholder"><?php echo substr(htmlspecialchars($job['company_name']), 0, 1); ?></div>
                        <?php endif; ?>
                    </div>
                    <span class="job-type-badge <?php echo $typeClass; ?>"><?php echo htmlspecialchars(ucfirst($job['type'])); ?></span>
                </div>
                <div class="job-card-body">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></p>
                    <div class="job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                        <span><i class="fas fa-money-bill-wave"></i> $<?php echo number_format(rand(50, 150)); ?>k/yr</span>
                    </div>
                </div>
                <div class="job-card-footer">
                    <span class="posted-time">Posted <?php echo rand(1, 7); ?> days ago</span>
                    <a href="job-details.php?id=<?php echo $job['id']; ?>" class="apply-now-btn">
                        Apply Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How It Works Section - Modern Steps -->
<section class="section-padding-modern how-it-works-modern">
    <div class="container">
        <div class="section-header-modern text-center" data-aos="fade-up">
            <h2 class="section-title-modern">How It Works</h2>
            <p class="section-subtitle">Get your dream job in just 3 simple steps</p>
        </div>
        <div class="steps-container">
            <div class="step-line" data-aos="fade-left"></div>
            <div class="steps-wrapper">
                <div class="step-item-modern" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-number">1</div>
                   
                    <h3>Create Account</h3>
                    <p>Sign up and create your professional profile in minutes.</p>
                </div>
                <div class="step-item-modern" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-number">2</div>
                    
                    <h3>Search Jobs</h3>
                    <p>Browse thousands of job openings with our advanced search filters.</p>
                </div>
                <div class="step-item-modern" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-number">3</div>
                    <div class="step-icon"><i class="fas fa-file-upload"></i></div>
                    <h3>Apply & Get Hired</h3>
                    <p>Apply to your chosen jobs and get closer to your dream career.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section - Animated Numbers -->
<section class="stats-section-modern">
    <div class="container">

            
    </div>
</section>

<!-- Featured Companies Section - Logo Grid -->
<section class="section-padding-modern companies-section-modern">
    <div class="container">
        <div class="section-header-modern" data-aos="fade-up">
            <h2 class="section-title-modern">Trusted by Leading Companies</h2>
            <a href="companies.php" class="view-all-link">View All Companies <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="companies-grid">
            <?php
            $companies = getAllCompanies();
            $i = 0; // Initialize counter
            foreach (array_slice($companies, 0, 8) as $company):
                $i++; // Increment counter
                $logo = $company['logo'];
                if (empty($logo)) {
                    $svg = '<svg width="150" height="60" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#f8f9fa"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="14" fill="#6c757d">' . htmlspecialchars($company['name']) . '</text></svg>';
                    $logo = 'data:image/svg+xml;base64,' . base64_encode($svg);
                }
            ?>
            <div class="company-logo-modern" data-aos="fade-up" data-aos-delay="<?php echo ($i % 4) * 100; ?>">
                <img src="<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($company['name']); ?>" class="img-responsive">
                <div class="company-hover">
                    <span><?php echo htmlspecialchars($company['name']); ?></span>
                    <span><?php echo rand(5, 30); ?> open positions</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section - Modern Carousel -->
<section class="testimonials-section-modern">
    <div class="container">
        <div class="section-header-modern text-center" data-aos="fade-up">
            <h2 class="section-title-modern">Success Stories</h2>
            <p class="section-subtitle">What our users say about us</p>
        </div>
        <div class="testimonials-carousel" data-aos="fade-up" data-aos-delay="100">
            <div class="testimonial-slide">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                     
                        <p class="testimonial-text">This platform connected me with my dream job. The process was seamless and professional, and I couldn't be happier with the outcome. Highly recommended for anyone serious about their career.</p>
                        <div class="testimonial-author">
                            <img src="https://images.unsplash.com/photo-1652795385719-3164c3dd8d14?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8am9iJTIwY2FydG9vbnxlbnwwfHwwfHx8MA%3D%3D" alt="Hein">
                            <div class="author-info">
                                <h4>Hein Thant Aung</h4>
                                <span>Senior Project Manager at Google</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-slide">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                       
                        <p class="testimonial-text">As an employer, I've found exceptional talent through this platform. The quality of candidates is outstanding, and the matching algorithm really understands our needs.</p>
                        <div class="testimonial-author">
                            <img src="https://media.istockphoto.com/id/1390310757/photo/workspace-with-computer-3d-render.jpg?s=612x612&w=0&k=20&c=fsGheCx_VEXEz4JerJHRBoJKlmbtZ9aBRYPBumtn2Xo=" alt="Zilly">
                            <div class="author-info">
                                <h4>Zilly</h4>
                                <span>HR Director at DesignHub</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section - Split Background -->
<section class="cta-section-modern">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2>Ready to Take the Next Step?</h2>
            <p class="cta-subtitle">Join thousands of professionals and companies achieving their goals</p>
        </div>
        <div class="cta-buttons">
            <div class="cta-button-seeker" data-aos="fade-right" data-aos-delay="100">
                <h3>Find Your Dream Job</h3>
                <p>Create your profile and discover opportunities that match your skills</p>
                <a href="register.php" class="btn-cta-modern seeker-btn">
                    <span>Job Seekers</span>
                    <i class="fas fa-user-tie"></i>
                </a>
            </div>
            <div class="cta-button-employer" data-aos="fade-left" data-aos-delay="100">
                <h3>Find Top Talent</h3>
                <p>Post jobs and connect with qualified candidates</p>
                <a href="register.php" class="btn-cta-modern employer-btn">
                    <span>Employers</span>
                    <i class="fas fa-building"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Blog Posts Section -->
<section class="section-padding-modern blog-section-modern">
    <div class="container">
        <div class="section-header-modern" data-aos="fade-up">
            <h2 class="section-title-modern">Career Insights</h2>
         
        </div>
        <div class="blog-grid">
            <article class="blog-card-modern" data-aos="fade-up">
                <div class="blog-image">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Career Growth">
                    <div class="blog-category">Career Growth</div>
                </div>
                <div class="blog-content">
                    <h3><a href="#">5 Ways to Advance Your Career in 2025</a></h3>
                    <p class="blog-excerpt">Discover the most effective strategies to take your career to the next level this year...</p>
                    <div class="blog-meta">
                        <span><i class="far fa-calendar"></i> June 15, 2025</span>
                        <span><i class="far fa-clock"></i> 5 min read</span>
                    </div>
                </div>
            </article>
            <article class="blog-card-modern" data-aos="fade-up" data-aos-delay="100">
                <div class="blog-image">
                    <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Interview Tips">
                    <div class="blog-category">Interview Tips</div>
                </div>
                <div class="blog-content">
                    <h3><a href="#">Acing the Virtual Interview: A Complete Guide</a></h3>
                    <p class="blog-excerpt">Master the art of remote interviews with these proven techniques and best practices...</p>
                    <div class="blog-meta">
                        <span><i class="far fa-calendar"></i> June 5, 2025</span>
                        <span><i class="far fa-clock"></i> 8 min read</span>
                    </div>
                </div>
            </article>
            <article class="blog-card-modern" data-aos="fade-up" data-aos-delay="200">
                <div class="blog-image">
                    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1469&q=80" alt="Resume Advice">
                    <div class="blog-category">Resume Advice</div>
                </div>
                <div class="blog-content">
                    <h3><a href="#">The Modern Resume: What Hiring Managers Really Want</a></h3>
                    <p class="blog-excerpt">Learn how to craft a resume that stands out in today's competitive job market...</p>
                    <div class="blog-meta">
                        <span><i class="far fa-calendar"></i> May 28, 2023</span>
                        <span><i class="far fa-clock"></i> 6 min read</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>