<?php
// includes/functions.php

function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatDate($date, $format = 'F d, Y')
{
    if (empty($date) || $date == '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

function calculateAge($birthdate)
{
    if (empty($birthdate) || $birthdate == '0000-00-00') {
        return 'N/A';
    }

    $birth = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birth)->y;
    return $age;
}

function calculateServiceYears($date_hired)
{
    if (empty($date_hired) || $date_hired == '0000-00-00') {
        return 'N/A';
    }

    $hired = new DateTime($date_hired);
    $today = new DateTime();
    $interval = $today->diff($hired);

    $years = $interval->y;
    $months = $interval->m;

    return $years . ' years, ' . $months . ' months';
}

function calculateRetirementDate($birthdate, $retirement_age = 65)
{
    if (empty($birthdate) || $birthdate == '0000-00-00') {
        return 'N/A';
    }

    $birth = new DateTime($birthdate);
    $birth->modify("+$retirement_age years");
    return $birth->format('F d, Y');
}

function getEmployeeCount()
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getRetirementDueCount($months_ahead = 6)
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT COUNT(*) as count FROM employees 
                  WHERE employment_status = 'Active' 
                  AND DATE_ADD(birth_date, INTERVAL 65 YEAR) 
                  BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :months MONTH)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':months', $months_ahead, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getStatusBadge($status)
{
    $badges = [
        'Active' => 'status-active',
        'Retired' => 'status-retired',
        'On-Leave' => 'status-onleave',
        'Resigned' => 'status-resigned',
        'AWOL' => 'status-awol',
        'Deceased' => 'status-deceased'
    ];

    $class = $badges[$status] ?? 'status-default';
    return '<span class="status-badge ' . $class . '">' . $status . '</span>';
}

function formatCurrency($amount)
{
    if (empty($amount)) {
        return '₱0.00';
    }
    return '₱' . number_format($amount, 2);
}

function checkPermission($required_role)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $user_role = $_SESSION['user_role'] ?? '';

    $role_hierarchy = [
        'Super Admin' => 5,
        'HR Admin' => 4,
        'HR Staff' => 3,
        'Supervisor' => 2,
        'Employee' => 1,
        'View Only' => 0
    ];

    $user_level = $role_hierarchy[$user_role] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;

    return $user_level >= $required_level;
}

function logActivity($action, $table_name = '', $record_id = '', $changes = '')
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO audit_log (user_id, employee_id, action, table_name, record_id, changes_made, ip_address, user_agent, browser, platform) 
                  VALUES (:user_id, :employee_id, :action, :table_name, :record_id, :changes, :ip, :agent, :browser, :platform)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':employee_id', $_SESSION['employee_id']);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':table_name', $table_name);
        $stmt->bindParam(':record_id', $record_id);
        $stmt->bindParam(':changes', $changes);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->bindValue(':agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->bindValue(':browser', get_browser_name($_SERVER['HTTP_USER_AGENT']));
        $stmt->bindValue(':platform', get_platform($_SERVER['HTTP_USER_AGENT']));

        $stmt->execute();
    } catch (Exception $e) {
        // Silently fail logging - don't break the main functionality
    }
}

function get_browser_name($user_agent)
{
    if (strpos($user_agent, 'Chrome') !== false)
        return 'Chrome';
    if (strpos($user_agent, 'Firefox') !== false)
        return 'Firefox';
    if (strpos($user_agent, 'Safari') !== false)
        return 'Safari';
    if (strpos($user_agent, 'Edge') !== false)
        return 'Edge';
    if (strpos($user_agent, 'MSIE') !== false)
        return 'Internet Explorer';
    return 'Unknown';
}

function get_platform($user_agent)
{
    if (strpos($user_agent, 'Windows') !== false)
        return 'Windows';
    if (strpos($user_agent, 'Mac') !== false)
        return 'Mac';
    if (strpos($user_agent, 'Linux') !== false)
        return 'Linux';
    if (strpos($user_agent, 'Android') !== false)
        return 'Android';
    if (strpos($user_agent, 'iOS') !== false)
        return 'iOS';
    return 'Unknown';
}

function sendEmailNotification($to, $subject, $message)
{
    // Implement email sending logic here
    // This is a placeholder function
    return true;
}

function generateRetirementAlerts()
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Check employees nearing retirement (6 months)
        $query = "SELECT e.employee_id, e.first_name, e.last_name, e.birth_date,
                  DATE_ADD(e.birth_date, INTERVAL 65 YEAR) as retirement_date,
                  DATEDIFF(DATE_ADD(e.birth_date, INTERVAL 65 YEAR), CURDATE()) as days_left
                  FROM employees e
                  WHERE e.employment_status = 'Active'
                  AND DATE_ADD(e.birth_date, INTERVAL 65 YEAR) 
                  BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                  AND NOT EXISTS (
                      SELECT 1 FROM retirement_alerts ra 
                      WHERE ra.employee_id = e.employee_id 
                      AND ra.alert_type = 'Retirement Due' 
                      AND ra.status = 'Unread'
                  )";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($employees as $employee) {
            $alert_message = "Employee {$employee['first_name']} {$employee['last_name']} is retiring on " .
                date('F d, Y', strtotime($employee['retirement_date'])) .
                " ({$employee['days_left']} days remaining)";

            $insert_query = "INSERT INTO retirement_alerts (employee_id, alert_type, alert_message, due_date, days_remaining, priority) 
                            VALUES (:employee_id, 'Retirement Due', :message, :due_date, :days_left, 'High')";

            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->bindParam(':employee_id', $employee['employee_id']);
            $insert_stmt->bindParam(':message', $alert_message);
            $insert_stmt->bindParam(':due_date', $employee['retirement_date']);
            $insert_stmt->bindParam(':days_left', $employee['days_left']);
            $insert_stmt->execute();
        }

        return count($employees);
    } catch (Exception $e) {
        return 0;
    }
}
?>