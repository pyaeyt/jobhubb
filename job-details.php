<?php
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: jobs.php');
    exit();
}

$jobId = (int)$_GET['id'];
$job = getJobById($jobId);

if (!$job) {
    header('Location: jobs.php');
    exit();
}

$content = 'job-details-content.php';
$title = htmlspecialchars($job['title']);
require_once("theme/templates.php");
?> 