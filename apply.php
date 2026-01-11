<?php
require_once 'includes/functions.php';
requireLogin();

// This script now ONLY handles the form submission logic and redirects.
// All HTML has been removed.

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobId = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $coverLetter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : '';
    $resumePath = null;
    $error = '';
    $redirectURL = 'job-details.php?id=' . $jobId; // Default redirect back to the job page

    // Basic validation
    if (empty($jobId) || empty($userId)) {
        $error = 'invalid_request';
    } elseif (empty($coverLetter)) {
        $error = 'cover_letter_required';
    }

    // Handle resume upload
    if (empty($error) && isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/resumes/';
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['resume']['type'], $allowedTypes)) {
            $error = 'invalid_file_type';
        } elseif ($_FILES['resume']['size'] > $maxSize) {
            $error = 'file_too_large';
        } else {
            // Create a unique filename to prevent overwriting
            $fileExtension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('resume_', true) . '.' . $fileExtension;
            $targetPath = $uploadDir . $uniqueName;

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
                $resumePath = $targetPath;
            } else {
                $error = 'upload_failed';
            }
        }
    } elseif (empty($error) && (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE)) {
        // A resume is now required
        $error = 'resume_required';
    } else if (empty($error) && $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = 'resume_required';
    }


    // If no errors, attempt to save the application
    if (empty($error)) {
        if (applyForJob($jobId, $userId, $coverLetter, $resumePath)) {
            // Success: redirect to the applications page
            header('Location: applications.php?success=applied');
            exit();
        } else {
            // Handle error, e.g., already applied or database error
            $error = 'application_failed';
        }
    }

    // If there was an error, redirect back to the job detail page with an error message
    header('Location: ' . $redirectURL . '&error=' . $error);
    exit();
}

// If accessed directly via GET request, just redirect away to the jobs page.
header('Location: jobs.php');
exit();
?> 