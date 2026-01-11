<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isEmployer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'employer';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}

function requireEmployer() {
    requireLogin();
    if (!isEmployer() && !isAdmin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}

// User functions
function registerUser($username, $email, $password, $fullName, $role = 'user') {
    $pdo = getDBConnection();
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$username, $email, $hashedPassword, $fullName, $role]);
}

function loginUser($email, $password) {
    $pdo = getDBConnection();

    // Redirect old admin email to new one
    if (strtolower($email) === 'admin@jobhub.com') {
        $email = 'admin@gmail.com';
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        return true;
    }
    return false;
}

function logoutUser() {
    session_destroy();
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getAllUsers() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY full_name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getUserById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateUser($id, $data, $password = null) {
    $pdo = getDBConnection();
    
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, password = ? WHERE id = ?");
        return $stmt->execute([$data['full_name'], $data['email'], $data['role'], $hashedPassword, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
        return $stmt->execute([$data['full_name'], $data['email'], $data['role'], $id]);
    }
}

function deleteUser($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// Job functions
function getAllJobs($limit = null, $offset = 0, $filters = [], $ignoreStatus = false) {
    $pdo = getDBConnection();
    
    $sql = "SELECT j.*, c.name as company_name, c.logo as company_logo, 
            (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) as application_count
            FROM jobs j 
            LEFT JOIN companies c ON j.company_id = c.id";
    
    $params = [];
    $whereClauses = [];
    
    if (!$ignoreStatus) {
        $whereClauses[] = "j.status = 'active'";
    }

    if (!empty($filters['employer_id'])) {
        $whereClauses[] = "j.employer_id = ?";
        $params[] = $filters['employer_id'];
    }

    if (!empty($filters['search'])) {
        $whereClauses[] = "(j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($filters['location'])) {
        $whereClauses[] = "j.location LIKE ?";
        $params[] = '%' . $filters['location'] . '%';
    }
    
    if (!empty($filters['type'])) {
        $whereClauses[] = "j.type = ?";
        $params[] = $filters['type'];
    }
    
    if (!empty($filters['experience'])) {
        $whereClauses[] = "j.experience_level = ?";
        $params[] = $filters['experience'];
    }
    
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    $sql .= " GROUP BY j.id ORDER BY j.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getJobById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT j.*, c.name as company_name, c.description as company_description, 
                          c.logo as company_logo, c.website as company_website, c.location as company_location,
                          u.full_name as employer_name 
                          FROM jobs j 
                          LEFT JOIN companies c ON j.company_id = c.id 
                          LEFT JOIN users u ON j.employer_id = u.id 
                          WHERE j.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createJob($data) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("INSERT INTO jobs (title, description, requirements, company_id, employer_id, 
                          location, type, salary_min, salary_max, experience_level) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['requirements'],
        $data['company_id'],
        $data['employer_id'],
        $data['location'],
        $data['type'],
        $data['salary_min'],
        $data['salary_max'],
        $data['experience_level']
    ]);
}

function updateJob($id, $data) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("UPDATE jobs SET title = ?, description = ?, requirements = ?, 
                          company_id = ?, location = ?, type = ?, salary_min = ?, 
                          salary_max = ?, experience_level = ?, status = ? WHERE id = ?");
    
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['requirements'],
        $data['company_id'],
        $data['location'],
        $data['type'],
        $data['salary_min'],
        $data['salary_max'],
        $data['experience_level'],
        $data['status'],
        $id
    ]);
}

function deleteJob($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    return $stmt->execute([$id]);
}

// Application functions
function applyForJob($jobId, $userId, $coverLetter, $resumePath = null) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, cover_letter, resume_path) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$jobId, $userId, $coverLetter, $resumePath]);
}

function getUserApplications($userId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT a.*, j.title as job_title, j.company_id, c.name as company_name 
                          FROM applications a 
                          JOIN jobs j ON a.job_id = j.id 
                          LEFT JOIN companies c ON j.company_id = c.id 
                          WHERE a.user_id = ? 
                          ORDER BY a.applied_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getAllApplications($employer_id = null) {
    $pdo = getDBConnection();

    $sql = "SELECT a.id as application_id, a.applied_at as date_applied, a.status,
                   j.title as job_title,
                   c.name as company_name,
                   u.full_name as applicant_name
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            LEFT JOIN companies c ON j.company_id = c.id
            JOIN users u ON a.user_id = u.id";

    $params = [];
    if ($employer_id !== null) {
        $sql .= " WHERE j.employer_id = ?";
        $params[] = $employer_id;
    }

    $sql .= " ORDER BY a.applied_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}


function getApplicationById($id) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT a.id as application_id, a.applied_at as date_applied, a.cover_letter, a.resume_path, a.status,
                                  j.title as job_title, j.employer_id,
                                  c.name as company_name, 
                                  u.full_name as applicant_name, u.email as applicant_email
                           FROM applications a 
                           JOIN jobs j ON a.job_id = j.id 
                           LEFT JOIN companies c ON j.company_id = c.id 
                           JOIN users u ON a.user_id = u.id 
                           WHERE a.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getJobApplications($jobId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT a.*, u.full_name, u.email
                          FROM applications a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE a.job_id = ? 
                          ORDER BY a.applied_at DESC");
    $stmt->execute([$jobId]);
    return $stmt->fetchAll();
}

function updateApplicationStatus($applicationId, $status) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $applicationId]);
}


// Company functions
function getAllCompanies($limit = null, $offset = 0) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM `companies`";
    if ($limit !== null) {
        $sql .= " LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare($sql);
    $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCompaniesForEmployer($employer_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE employer_id = ? ORDER BY name");
    $stmt->execute([$employer_id]);
    return $stmt->fetchAll();
}

function getCompanyById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getJobsByCompany($company_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? AND status = 'active' ORDER BY created_at DESC");
    $stmt->execute([$company_id]);
    return $stmt->fetchAll();
}

function createCompany($data, $logoPath = null) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("INSERT INTO companies (name, description, industry, website, location, logo) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['industry'],
        $data['website'],
        $data['location'],
        $logoPath
    ]);
}

function updateCompany($id, $data, $logoPath = null) {
    $pdo = getDBConnection();
    
    $sql = "UPDATE companies SET name = ?, description = ?, industry = ?, website = ?, location = ?";
    $params = [
        $data['name'],
        $data['description'],
        $data['industry'],
        $data['website'],
        $data['location']
    ];
    
    if ($logoPath !== null) {
        $sql .= ", logo = ?";
        $params[] = $logoPath;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}


// Utility functions
function formatSalary($min, $max) {
    if (!empty($min) && !empty($max)) {
        return '$' . number_format($min) . ' - $' . number_format($max);
    } elseif (!empty($min)) {
        return '$' . number_format($min);
    }
    return 'Not specified';
}

function formatDate($date) {
    return date("M d, Y", strtotime($date));
}

function formatDateTime($date) {
    return date("M d, Y, g:i a", strtotime($date));
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active': return 'success';
        case 'inactive': return 'secondary';
        case 'pending': return 'warning';
        case 'closed': return 'danger';
        default: return 'light';
    }
}

function getApplicationStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'warning';
        case 'accepted': return 'success';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

function uploadFile($file, $directory = 'uploads/') {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $fileName = uniqid() . '-' . basename($file['name']);
        $targetPath = $directory . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath;
        }
    }
    return null;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function getDashboardStats($employer_id = null) {
    $pdo = getDBConnection();
    $stats = [];

    $whereJobs = '';
    $whereApplications = '';
    $params = [];

    if ($employer_id !== null) {
        $whereJobs = ' WHERE employer_id = ?';
        $whereApplications = ' WHERE j.employer_id = ?';
        $params[] = $employer_id;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs" . $whereJobs . (empty($whereJobs) ? " WHERE status = 'active'" : " AND status = 'active'"));
    $stmt->execute($params);
    $stats['total_jobs'] = $stmt->fetchColumn();

    if ($employer_id === null) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $stats['total_users'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employer'");
        $stats['total_employers'] = $stmt->fetchColumn();
    } else {
        $stats['total_users'] = 0;
        $stats['total_employers'] = 0;
    }

    $sql = "SELECT COUNT(a.id) FROM applications a";
    if ($employer_id !== null) {
        $sql .= " JOIN jobs j ON a.job_id = j.id";
    }
    $sql .= $whereApplications;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $stats['total_applications'] = $stmt->fetchColumn();

    return $stats;
}
?> 