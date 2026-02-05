<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . 'index.php');
    exit();
}

// Get user information
$user_role = $_SESSION['user_role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: 70px;
        }

        .navbar {
            background: linear-gradient(135deg, var(--secondary-color), #1a252f);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.5rem;
        }

        .navbar-brand i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            width: 250px;
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            z-index: 1000;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed+.main-content {
            margin-left: 70px;
        }

        .user-profile {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
        }

        .user-info h5 {
            margin: 0;
            font-weight: 600;
        }

        .user-info small {
            color: #7f8c8d;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-menu li {
            border-bottom: 1px solid #eee;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #34495e;
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-menu a:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
            padding-left: 25px;
        }

        .nav-menu a.active {
            background-color: var(--primary-color);
            color: white;
            border-left: 4px solid var(--secondary-color);
        }

        .nav-menu i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        .badge {
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 10px;
        }

        .content-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            border: none;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-retired {
            background: #f8d7da;
            color: #721c24;
        }

        .status-onleave {
            background: #fff3cd;
            color: #856404;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar .menu-text {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }

            .user-info {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand" href="<?php echo SITE_URL; ?>modules/dashboard.php">
                <i class="fas fa-user-tie"></i> <?php echo SITE_NAME; ?>
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>modules/profile.php"><i
                                    class="fas fa-user"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>modules/settings.php"><i
                                    class="fas fa-cog"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>logout.php"><i
                                    class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-info">
                <h5><?php echo htmlspecialchars($username); ?></h5>
                <small><?php echo $user_role; ?></small>
            </div>
        </div>

        <ul class="nav-menu">
            <li><a href="<?php echo SITE_URL; ?>modules/dashboard.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span>
                </a></li>

            <?php if (in_array($user_role, ['Super Admin', 'HR Admin', 'HR Staff'])): ?>
                <li><a href="<?php echo SITE_URL; ?>modules/employees/list_employees.php"
                        class="<?php echo strpos($_SERVER['PHP_SELF'], 'employees') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> <span class="menu-text">Employees</span>
                        <span class="badge bg-primary ms-auto"><?php echo getEmployeeCount(); ?></span>
                    </a></li>
            <?php endif; ?>

            <li><a href="<?php echo SITE_URL; ?>modules/my_profile.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-id-card"></i> <span class="menu-text">My Profile</span>
                </a></li>

            <?php if (in_array($user_role, ['Super Admin', 'HR Admin', 'HR Staff'])): ?>
                <li><a href="<?php echo SITE_URL; ?>modules/retirement/retirement_list.php"
                        class="<?php echo strpos($_SERVER['PHP_SELF'], 'retirement') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-user-check"></i> <span class="menu-text">Retirement</span>
                        <span class="badge bg-warning ms-auto"><?php echo getRetirementDueCount(); ?></span>
                    </a></li>

                <li><a href="<?php echo SITE_URL; ?>modules/reports/active_employees.php"
                        class="<?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i> <span class="menu-text">Reports</span>
                    </a></li>

                <li><a href="<?php echo SITE_URL; ?>modules/documents.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> <span class="menu-text">Documents</span>
                    </a></li>
            <?php endif; ?>

            <?php if ($user_role == 'Super Admin'): ?>
                <li><a href="<?php echo SITE_URL; ?>modules/users.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-shield"></i> <span class="menu-text">User Management</span>
                    </a></li>

                <li><a href="<?php echo SITE_URL; ?>modules/system_settings.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cogs"></i> <span class="menu-text">System Settings</span>
                    </a></li>
            <?php endif; ?>

            <li><a href="<?php echo SITE_URL; ?>logout.php">
                    <i class="fas fa-sign-out-alt"></i> <span class="menu-text">Logout</span>
                </a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-header">
            <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>modules/dashboard.php"><i
                                class="fas fa-home"></i> Home</a></li>
                    <?php if (isset($page_title)): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title; ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>

        <div class="container-fluid">