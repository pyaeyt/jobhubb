<?php
require_once '../includes/functions.php';
header('Content-Type: application/json');

$employer_id = isAdmin() ? null : $_SESSION['user_id'];
$stats = getDashboardStats($employer_id);

$pdo = getDBConnection();

// Count all users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Count all companies
$stmt = $pdo->query("SELECT COUNT(*) FROM companies");
$total_companies = $stmt->fetchColumn();

// Count all applications
$stmt = $pdo->query("SELECT COUNT(*) FROM applications");
$total_applications = $stmt->fetchColumn();

// Count all jobs
$stmt = $pdo->query("SELECT COUNT(*) FROM jobs WHERE status = 'active'");
$total_jobs = $stmt->fetchColumn();

$response = [
    'success' => true,
    'total_jobs' => $total_jobs,
    'total_users' => $total_users,
    'total_companies' => $total_companies,
    'total_applications' => $total_applications
];
echo json_encode($response); 