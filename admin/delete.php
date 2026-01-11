<?php
require_once '../includes/functions.php';
requireAdmin();

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];

    if ($type === 'company') {
        $pdo = getDBConnection();
        try {
            $pdo->beginTransaction();
    
            // Get all job IDs for the company
            $stmt = $pdo->prepare("SELECT id FROM jobs WHERE company_id = ?");
            $stmt->execute([$id]);
            $job_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            if (!empty($job_ids)) {
                // Create placeholders for the IN clause
                $in_clause = implode(',', array_fill(0, count($job_ids), '?'));
                
                // Delete applications for those jobs
                $stmt = $pdo->prepare("DELETE FROM applications WHERE job_id IN ($in_clause)");
                $stmt->execute($job_ids);
    
                // Delete jobs for the company
                $stmt = $pdo->prepare("DELETE FROM jobs WHERE company_id = ?");
                $stmt->execute([$id]);
            }
    
            // Delete the company
            $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
            $stmt->execute([$id]);
    
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            // You can log the error here if you have a logging system
            // For example: error_log($e->getMessage());
            $_SESSION['error_message'] = "Error deleting company. Please try again.";
        }
        
        header('Location: index.php?p=companies');
        exit();
    }

    if ($type === 'job') {
        deleteJob($id);
        header('Location: index.php?p=jobs&success=delete');
        exit();
    }
    
    if ($type === 'user') {
        deleteUser($id);
        header('Location: index.php?p=users&success=delete');
        exit();
    }

    if ($type === 'application') {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?p=applications&success=delete');
        exit();
    }
}

// Redirect to dashboard if type or id is not set
header('Location: index.php');
exit();
?> 