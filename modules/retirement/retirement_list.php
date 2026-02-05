<?php
// modules/retirement/retirement_list.php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireRole('HR Staff');

$page_title = 'Retirement Management';
require_once '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Handle retirement action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $employee_id = clean_input($_GET['id']);
    $action = clean_input($_GET['action']);
    
    try {
        if ($action == 'process') {
            // Calculate retirement benefits
            $query = "SELECT e.*, ed.monthly_salary, ed.position_title,
                      TIMESTAMPDIFF(YEAR, e.date_hired, CURDATE()) as years_of_service,
                      TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) as age
                      FROM employees e
                      LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id
                      WHERE e.employee_id = :employee_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employee) {
                // Calculate benefits
                $monthly_pension = $employee['monthly_salary'] * 0.90; // 90% of last salary
                $terminal_leave = calculateTerminalLeave($employee_id, $employee['years_of_service']);
                
                // Insert retirement info
                $retirement_query = "INSERT INTO retirement_info (
                    employee_id, retirement_status, retirement_type,
                    date_of_retirement, date_of_application,
                    years_in_service, age_at_retirement,
                    last_salary_rate, monthly_pension, terminal_leave_benefits,
                    requirements_status, clearance_status
                ) VALUES (
                    :employee_id, 'Applied', 'Compulsory',
                    CURDATE(), CURDATE(),
                    :years_service, :age,
                    :last_salary, :monthly_pension, :terminal_leave,
                    'Incomplete', 'Pending'
                )";
                
                $retirement_stmt = $db->prepare($retirement_query);
                $retirement_stmt->bindParam(':employee_id', $employee_id);
                $retirement_stmt->bindParam(':years_service', $employee['years_of_service']);
                $retirement_stmt->bindParam(':age', $employee['age']);
                $retirement_stmt->bindParam(':last_salary', $employee['monthly_salary']);
                $retirement_stmt->bindParam(':monthly_pension', $monthly_pension);
                $retirement_stmt->bindParam(':terminal_leave', $terminal_leave);
                $retirement_stmt->execute();
                
                // Update employee status
                $update_query = "UPDATE employees SET 
                    employment_status = 'Retired',
                    date_retired = CURDATE(),
                    updated_at = NOW()
                    WHERE employee_id = :employee_id";
                
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(':employee_id', $employee_id);
                $update_stmt->execute();
                
                // Generate retirement checklist
                generateRetirementChecklist($employee_id);
                
                $_SESSION['success'] = "Retirement processed successfully for employee ID: $employee_id";
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing retirement: " . $e->getMessage();
    }
    
    header('Location: retirement_list.php');
    exit();
}

// Get employees nearing retirement
$query = "SELECT e.employee_id, e.deped_id, e.first_name, e.last_name, e.middle_name,
          e.birth_date, e.date_hired, e.employment_status,
          ed.position_title, ed.salary_grade, ed.monthly_salary,
          TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) as age,
          TIMESTAMPDIFF(YEAR, e.date_hired, CURDATE()) as years_of_service,
          DATE_ADD(e.birth_date, INTERVAL 65 YEAR) as retirement_date,
          DATEDIFF(DATE_ADD(e.birth_date, INTERVAL 65 YEAR), CURDATE()) as days_to_retirement,
          ri.retirement_status
          FROM employees e
          LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id
          LEFT JOIN retirement_info ri ON e.employee_id = ri.employee_id
          WHERE e.employment_status = 'Active'
          ORDER BY retirement_date ASC";

$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get already retired employees
$retired_query = "SELECT e.*, ed.position_title, ri.date_of_retirement, ri.retirement_type
                  FROM employees e
                  LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id
                  LEFT JOIN retirement_info ri ON e.employee_id = ri.employee_id
                  WHERE e.employment_status = 'Retired'
                  ORDER BY ri.date_of_retirement DESC";

$retired_stmt = $db->prepare($retired_query);
$retired_stmt->execute();
$retired_employees = $retired_stmt->fetchAll(PDO::FETCH_ASSOC);

function calculateTerminalLeave($employee_id, $years_of_service) {
    // Simplified terminal leave calculation
    // 1.25 days per month for first 20 years, 1.5 days per month thereafter
    if ($years_of_service <= 20) {
        $days = $years_of_service * 12 * 1.25;
    } else {
        $days = (20 * 12 * 1.25) + (($years_of_service - 20) * 12 * 1.5);
    }
    
    // Add unused leave credits (simplified)
    $days += 15; // Assuming 15 unused leave days
    
    // Get daily rate
    global $db;
    $salary_query = "SELECT monthly_salary FROM employment_details WHERE employee_id = :employee_id";
    $salary_stmt = $db->prepare($salary_query);
    $salary_stmt->bindParam(':employee_id', $employee_id);
    $salary_stmt->execute();
    $salary = $salary_stmt->fetch(PDO::FETCH_ASSOC);
    
    $daily_rate = $salary['monthly_salary'] / 22; // 22 working days per month
    return $days * $daily_rate;
}

function generateRetirementChecklist($employee_id) {
    global $db;
    
    $requirements = [
        'GSIS Requirements' => [
            'Application for Retirement Benefits',
            'Certified True Copy of Service Record',
            'Clearance from Money/Property Accountabilities',
            'Certified Photocopy of Birth/Baptismal Certificate',
            'Marriage Contract (if applicable)',
            'Death Certificate of Member (if applicable)'
        ],
        'Accounting Requirements' => [
            'Terminal Leave Benefits Application',
            'Last Salary and Other Benefits',
            'Clearance from Property Accountability',
            'Tax Clearance'
        ],
        'HR Requirements' => [
            'Clearance Form',
            'Turnover of Properties/Responsibilities',
            'ID Surrender',
            'Certificate of Employment',
            'Service Record'
        ]
    ];
    
    foreach ($requirements as $category => $items) {
        foreach ($items as $item) {
            $check_query = "INSERT INTO retirement_checklist 
                           (employee_id, requirement_category, requirement_item, status, date_required)
                           VALUES (:employee_id, :category, :item, 'Pending', DATE_ADD(CURDATE(), INTERVAL 7 DAY))";
            
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':employee_id', $employee_id);
            $check_stmt->bindParam(':category', $category);
            $check_stmt->bindParam(':item', $item);
            $check_stmt->execute();
        }
    }
}
?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Retirement Management</h6>
        <div>
            <a href="retirement_report.php" class="btn btn-success btn-sm">
                <i class="fas fa-chart-pie"></i> Retirement Report
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="retirementTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                    Upcoming Retirements
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="retired-tab" data-bs-toggle="tab" data-bs-target="#retired" type="button">
                    Retired Employees
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="checklist-tab" data-bs-toggle="tab" data-bs-target="#checklist" type="button">
                    Retirement Checklist
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="retirementTabContent">
            <!-- Upcoming Retirements Tab -->
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Age</th>
                                <th>Years of Service</th>
                                <th>Retirement Date</th>
                                <th>Days Left</th>
                                <th>Estimated Pension</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <?php 
                                $retirement_date = new DateTime($employee['retirement_date']);
                                $today = new DateTime();
                                $interval = $today->diff($retirement_date);
                                $days_left = $interval->days;
                                $is_past_due = $retirement_date < $today;
                                
                                $estimated_pension = $employee['monthly_salary'] * 0.90;
                                ?>
                                <tr class="<?php echo $is_past_due ? 'table-danger' : ($days_left <= 30 ? 'table-warning' : ''); ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($employee['last_name'] . ', ' . $employee['first_name']); ?></strong><br>
                                        <small class="text-muted">ID: <?php echo htmlspecialchars($employee['employee_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['position_title']); ?></td>
                                    <td><?php echo $employee['age']; ?> years</td>
                                    <td><?php echo $employee['years_of_service']; ?> years</td>
                                    <td>
                                        <?php echo formatDate($employee['retirement_date']); ?><br>
                                        <small class="text-muted"><?php echo $is_past_due ? 'Past Due' : ''; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $is_past_due ? 'bg-danger' : ($days_left <= 30 ? 'bg-warning' : ($days_left <= 90 ? 'bg-info' : 'bg-success')); ?>">
                                            <?php echo $days_left; ?> days
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($estimated_pension); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="process_retirement.php?id=<?php echo $employee['employee_id']; ?>" 
                                               class="btn btn-primary btn-sm" title="Process Retirement">
                                                <i class="fas fa-user-check"></i> Process
                                            </a>
                                            <a href="view_retirement.php?id=<?php echo $employee['employee_id']; ?>" 
                                               class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="send_notice.php?id=<?php echo $employee['employee_id']; ?>" 
                                               class="btn btn-warning btn-sm" title="Send Notice">
                                                <i class="fas fa-bell"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Retired Employees Tab -->
            <div class="tab-pane fade" id="retired" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Retirement Date</th>
                                <th>Retirement Type</th>
                                <th>Years of Service</th>
                                <th>Age at Retirement</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($retired_employees as $employee): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($employee['last_name'] . ', ' . $employee['first_name']); ?></strong><br>
                                        <small class="text-muted">ID: <?php echo htmlspecialchars($employee['employee_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['position_title']); ?></td>
                                    <td><?php echo formatDate($employee['date_of_retirement']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($employee['retirement_type']); ?></span>
                                    </td>
                                    <td><?php echo calculateServiceYears($employee['date_hired']); ?></td>
                                    <td><?php echo calculateAge($employee['birth_date']); ?> years</td>
                                    <td><?php echo getStatusBadge($employee['employment_status']); ?></td>
                                    <td>
                                        <a href="view_retirement_details.php?id=<?php echo $employee['employee_id']; ?>" 
                                           class="btn btn-info btn-sm" title="View Retirement Details">
                                            <i class="fas fa-file-alt"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Retirement Checklist Tab -->
            <div class="tab-pane fade" id="checklist" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Retirement Requirements Checklist</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $checklist_query = "SELECT rc.*, e.first_name, e.last_name 
                                                   FROM retirement_checklist rc
                                                   LEFT JOIN employees e ON rc.employee_id = e.employee_id
                                                   WHERE rc.status != 'Verified'
                                                   ORDER BY rc.date_required ASC";
                                $checklist_stmt = $db->prepare($checklist_query);
                                $checklist_stmt->execute();
                                $checklists = $checklist_stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($checklists) > 0):
                                ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Requirement</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Date Required</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($checklists as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($item['requirement_item']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo $item['requirement_category']; ?></span></td>
                                                <td>
                                                    <?php 
                                                    $status_badges = [
                                                        'Pending' => 'bg-warning',
                                                        'Submitted' => 'bg-info',
                                                        'Verified' => 'bg-success',
                                                        'Rejected' => 'bg-danger'
                                                    ];
                                                    ?>
                                                    <span class="badge <?php echo $status_badges[$item['status']] ?? 'bg-secondary'; ?>">
                                                        <?php echo $item['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($item['date_required']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="updateChecklistStatus(<?php echo $item['checklist_id']; ?>, 'Verified')">
                                                        <i class="fas fa-check"></i> Verify
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No pending retirement checklists.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateChecklistStatus(checklist_id, status) {
    if (confirm('Are you sure you want to update this checklist item?')) {
        window.location.href = 'update_checklist.php?id=' + checklist_id + '&status=' + status;
    }
}
</script>

<?php
require_once '../../includes/footer.php';
?>