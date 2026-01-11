<section id="content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <h2><?php echo htmlspecialchars($job['title']); ?></h2>
        <p>
          <i class="fa fa-building-o"></i> <a href="company-details.php?id=<?php echo $job['company_id']; ?>"><?php echo htmlspecialchars($job['company_name']); ?></a>
          &nbsp;&nbsp;|&nbsp;&nbsp;
          <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($job['location']); ?>
        </p>
        <hr>

        <h4>Job Description</h4>
        <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

        <h4>Requirements</h4>
        <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
      </div>
      <div class="col-md-4">
        <div class="well">
          <h4>Job Summary</h4>
          <p>
            <strong>Duration:</strong> <?php echo ucfirst(str_replace('-', ' ', $job['type'])); ?><br>
            <strong>Experience:</strong> <?php echo ucfirst($job['experience_level']); ?><br>
            <strong>Salary:</strong> <?php echo formatSalary($job['salary_min'], $job['salary_max']); ?><br>
            <strong>Posted:</strong> <?php echo formatDate($job['created_at']); ?>
          </p>
        </div>
      </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <h3>Apply for this Job</h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        switch ($_GET['error']) {
                            case 'application_failed':
                                echo 'There was an error submitting your application. You may have already applied for this job.';
                                break;
                            case 'cover_letter_required':
                                echo 'The cover letter is a required field.';
                                break;
                            case 'resume_required':
                                echo 'A resume file is required. Please choose a file to upload.';
                                break;
                            case 'invalid_file_type':
                                echo 'Invalid file type. Please upload a PDF or Word document.';
                                break;
                            case 'file_too_large':
                                echo 'The uploaded file is too large. Please upload a file smaller than 2MB.';
                                break;
                            case 'upload_failed':
                                echo 'There was an error uploading your resume. Please try again.';
                                break;
                            default:
                                echo 'An unexpected error occurred. Please try again.';
                                break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <form method="POST" action="apply.php" enctype="multipart/form-data">
                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                    <div class="form-group mb-3">
                        <label for="cover_letter"><h4>Cover Letter</h4></label>
                        <textarea name="cover_letter" id="cover_letter" class="form-control" rows="8" required placeholder="Explain why you are the best candidate..."></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="resume"><h4>Resume (PDF or Word)</h4></label>
                        <input type="file" name="resume" id="resume" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                </form>
            <?php else: ?>
                <p>
                    <a href="login.php" data-toggle="modal" data-target="#loginModal">Login</a> to apply for this job.
                </p>
            <?php endif; ?>
        </div>
    </div>
  </div>
</section> 