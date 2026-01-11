<?php
ini_set('display_errors', 1);

require_once '../includes/functions.php';
requireEmployer(); // Use requireEmployer to allow both admins and employers

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['application_id'], $_POST['status'])) {
        $applicationId = (int)$_POST['application_id'];
        $status = $_POST['status'];
        
        // Security check: Ensure the current user has permission to view this application
        $application = getApplicationById($applicationId);
        if (!$application || (!isAdmin() && $application['employer_id'] !== $_SESSION['user_id'])) {
            header("Location: index.php?p=applications");
            exit();
        }

        // Prevent status change if already accepted or rejected
        if (in_array($application['status'], ['accepted', 'rejected'])) {
            header("Location: index.php?p=application-view&id=$applicationId&status=locked");
            exit();
        }

        $allowed_statuses = ['pending', 'accepted', 'rejected'];
        if (in_array($status, $allowed_statuses)) {
            if (updateApplicationStatus($applicationId, $status)) {
                // Redirect back to the application view with a success message
                header("Location: index.php?p=application-view&id=$applicationId&status=success");
                exit();
            }
        }
    }
}

// If not a POST request, or if update fails, or missing data, redirect to the applications list
header("Location: index.php?p=applications");
exit();

/*
ini_set('display_errors', 1);

require_once '../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['application_id'], $_POST['status'])) {
        $applicationId = (int)$_POST['application_id'];
        $status = $_POST['status'];
        
        $allowed_statuses = ['pending', 'accepted', 'rejected'];
        if (in_array($status, $allowed_statuses)) {
            $result = updateApplicationStatus($applicationId, $status);
            
            // --- Simple Debugging ---
            if ($result) {
                echo "SUCCESS: Application ID $applicationId status updated to '$status'.";
            } else {
                echo "FAILURE: Could not update application ID $applicationId to status '$status'.";
            }
            die(); // Stop execution to show the message
        }
    }
}

// If not a POST request, or if update fails, or missing data, redirect to the applications list
header("Location: index.php?p=applications");
exit();
*/ 