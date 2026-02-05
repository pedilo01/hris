<?php
// modules/dashboard.php
require_once '../config/database.php';
require_once '../config/constants.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$page_title = 'Dashboard';
require_once '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Generate retirement alerts
$alerts_generated = generateRetirementAlerts();

// Get dashboard statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM employees WHERE employment_status = 'Active') as total_employees,
    (SELECT COUNT(*) FROM employees WHERE employment_status = 'Retired') as retired_employees,
    (SELECT COUNT(*) FROM employees WHERE employment_status = 'On-Leave') as onleave_employees,
    (SELECT COUNT(*) FROM retirement_info WHERE retirement_status = 'Eligible') as retirement_eligible,
    (SELECT COUNT(*) FROM retirement_alerts WHERE status = 'Unread') as unread_alerts";

$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get upcoming retirements
$retirements_query = "SELECT e.employee_id, e.first_name, e.last_name,
                      DATE_ADD(e.birth_date, INTERVAL 65 YEAR) as retirement_date,
                      DATEDIFF(DATE_ADD(e.birth_date, INTERVAL 65 YEAR), CURDATE()) as days_left
                      FROM employees e
                      WHERE e.employment_status = 'Active'
                      AND DATE_ADD(e.birth_date, INTERVAL 65 YEAR) 
                      BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
                      ORDER BY retirement_date ASC
                      LIMIT 10";

$retirements_stmt = $db->prepare($retirements_query);
$retirements_stmt->execute();
$upcoming_retirements = $retirements_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent activities
$activities_query = "SELECT al.*, e.first_name, e.last_name 
                     FROM audit_log al
                     LEFT JOIN employees e ON al.employee_id = e.employee_id
                     ORDER BY al.timestamp DESC
                     LIMIT 10";

$activities_stmt = $db->prepare($activities_query);
$activities_stmt->execute();
$recent_activities = $activities_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Active Employees</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_employees']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Retired Employees</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['retired_employees']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Retirement Eligible</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['retirement_eligible']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Unread Alerts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['unread_alerts']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Retirements -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Retirements (Next 6 Months)</h6>
                <a href="../modules/retirement/retirement_list.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Retirement Date</th>
                                <th>Days Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($upcoming_retirements) > 0): ?>
                                <?php foreach ($upcoming_retirements as $retirement): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($retirement['first_name'] . ' ' . $retirement['last_name']); ?></td>
                                        <td><?php echo formatDate($retirement['retirement_date']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $retirement['days_left'] <= 30 ? 'bg-danger' : ($retirement['days_left'] <= 90 ? 'bg-warning' : 'bg-info'); ?>">
                                                <?php echo $retirement['days_left']; ?> days
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No upcoming retirements</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
            </div>
            <div class="card-body">
                <div class="activities-list">
                    <?php if (count($recent_activities) > 0): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item mb-3">
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <strong><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></strong>
                                        <?php echo htmlspecialchars($activity['action']); ?>
                                    </div>
                                    <div class="activity-time text-muted">
                                        <small><?php echo formatDate($activity['timestamp'], 'M d, Y h:i A'); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No recent activities</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Employee Status Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (checkPermission('HR Staff')): ?>
                    <div class="col-md-6 mb-3">
                        <a href="../modules/employees/add_employee.php" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Add Employee
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="../modules/reports/active_employees.php" class="btn btn-success btn-block">
                            <i class="fas fa-file-export"></i> Generate Report
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-md-6 mb-3">
                        <a href="my_profile.php" class="btn btn-info btn-block">
                            <i class="fas fa-id-card"></i> My Profile
                        </a>
                    </div>
                    
                    <?php if (checkPermission('HR Admin')): ?>
                    <div class="col-md-6 mb-3">
                        <a href="../modules/retirement/retirement_report.php" class="btn btn-warning btn-block">
                            <i class="fas fa-chart-pie"></i> Retirement Report
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-md-6 mb-3">
                        <a href="../logout.php" class="btn btn-danger btn-block">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart for Employee Status Distribution
$(document).ready(function() {
    var ctx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Retired', 'On-Leave', 'Resigned', 'AWOL'],
            datasets: [{
                label: 'Number of Employees',
                data: [
                    <?php echo $stats['total_employees']; ?>,
                    <?php echo $stats['retired_employees']; ?>,
                    <?php echo $stats['onleave_employees']; ?>,
                    <?php 
                        $resigned_query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Resigned'";
                        $resigned_stmt = $db->prepare($resigned_query);
                        $resigned_stmt->execute();
                        $resigned = $resigned_stmt->fetch(PDO::FETCH_ASSOC);
                        echo $resigned['count'];
                    ?>,
                    <?php 
                        $awol_query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'AWOL'";
                        $awol_stmt = $db->prepare($awol_query);
                        $awol_stmt->execute();
                        $awol = $awol_stmt->fetch(PDO::FETCH_ASSOC);
                        echo $awol['count'];
                    ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Employee Status Distribution'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<?php
require_once '../includes/footer.php';
?>