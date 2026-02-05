<?php
// includes/auth.php
session_start();
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . 'index.php');
        exit();
    }
}

function requireRole($required_role)
{
    requireLogin();

    if (!checkPermission($required_role)) {
        $_SESSION['error'] = 'You do not have permission to access this page.';
        header('Location: ' . SITE_URL . 'modules/dashboard.php');
        exit();
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getCurrentUser()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT u.*, e.* FROM users u 
                  LEFT JOIN employees e ON u.employee_id = e.employee_id 
                  WHERE u.user_id = :user_id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}
?>