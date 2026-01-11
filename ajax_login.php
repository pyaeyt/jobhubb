<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required.';
    } else {
        if (loginUser($email, $password)) {
            $redirect_url = 'index.php'; // Default home page
            if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'employer') {
                $redirect_url = 'admin/index.php'; // Admin/Employer dashboard
            }
            $response = ['status' => 'success', 'redirect' => $redirect_url];
        } else {
            $response['message'] = 'Invalid email or password.';
        }
    }
}

echo json_encode($response);
?> 