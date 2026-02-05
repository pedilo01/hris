<?php
// logout.php
session_start();

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    require_once 'config/database.php';
    require_once 'includes/functions.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $query = "INSERT INTO audit_log (user_id, employee_id, action, ip_address, user_agent) 
                  VALUES (:user_id, :employee_id, 'Logout', :ip, :agent)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();
    } catch (Exception $e) {
        // Silently fail logging
    }
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: index.php');
exit();
?>