<?php
require_once 'includes/functions.php';

// Pagination settings
$perPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total companies count
$pdo = getDBConnection();
$totalCompanies = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
$totalPages = ceil($totalCompanies / $perPage);

// Fetch companies for current page
$companies = getAllCompanies($perPage, $offset);

// Set content and title for template
$content = 'company-content.php';
$title = 'Companies';
$active_companies = 'active';

// Pass pagination variables to template
$pagination = [
    'page' => $page,
    'totalPages' => $totalPages
];

// Include the template
require_once("theme/templates.php");
?>