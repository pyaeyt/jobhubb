<?php
require_once 'includes/functions.php';

// Get filter parameters
$companyFilter = isset($_GET['company']) ? (int)$_GET['company'] : null;
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

// Build filters array
$filters = [];
if ($searchFilter) $filters['search'] = $searchFilter;
if ($locationFilter) $filters['location'] = $locationFilter;
if ($typeFilter) $filters['type'] = $typeFilter;

// Get jobs with filters
$jobs = getAllJobs(null, 0, $filters);

// If company filter is applied, filter the results
if ($companyFilter) {
    $jobs = array_filter($jobs, function($job) use ($companyFilter) {
        return $job['company_id'] == $companyFilter;
    });
}

// Get company name if filtering by company
$companyName = '';
if ($companyFilter) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$companyFilter]);
    $company = $stmt->fetch();
    $companyName = $company ? $company['name'] : '';
}

// Set content and title for template
$content = 'jobs-content.php';
$title = $companyName ? "Jobs at $companyName" : 'All Jobs';
$active_jobs = 'active';

// Include the template
require_once("theme/templates.php");
?> 