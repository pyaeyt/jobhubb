<?php 
require_once 'includes/functions.php';

// Check if an ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to the companies list or show an error
    header('Location: companies.php');
    exit;
}

$company_id = (int)$_GET['id'];
$company = getCompanyById($company_id);
$jobs = getJobsByCompany($company_id);

if (!$company) {
    // Company not found, redirect
    header('Location: companies.php');
    exit;
}

// Set content and title for template
$content = 'company-details-content.php';
$title = htmlspecialchars($company['name']);

// Include the template
require_once("theme/templates.php");
?> 