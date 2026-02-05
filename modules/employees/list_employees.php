<?php
// modules/employees/list_employees.php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireRole('HR Staff');

$page_title = 'Employee List';
require_once '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Handle search
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';

// Build query
$query = "SELECT e.*, ed.position_title, ed.salary_grade 
          FROM employees e 
          LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (e.first_name LIKE :search OR e.last_name LIKE :search 
                OR e.employee_id LIKE :search OR e.deped_id LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND e.employment_status = :status";
    $params[':status'] = $status_filter;
}

$query .= " ORDER BY e.last_name, e.first_name";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Employee List</h6>
        <div>
            <a href="add_employee.php" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add New Employee
            </a>
            <a href="export_employees.php" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, ID, or DepEd ID" 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="Active" <?php echo $status_filter == 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Retired" <?php echo $status_filter == 'Retired' ? 'selected' : ''; ?>>Retired</option>
                        <option value="On-Leave" <?php echo $status_filter == 'On-Leave' ? 'selected' : ''; ?>>On-Leave</option>
                        <option value="Resigned" <?php echo $status_filter == 'Resigned' ? 'selected' : ''; ?>>Resigned</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Employee Table -->
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Salary Grade</th>
                        <th>Status</th>
                        <th>Date Hired</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <?php 
                        $age = calculateAge($employee['birth_date']);
                        $service_years = calculateServiceYears($employee['date_hired']);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($employee['employee_id']); ?></strong><br>
                                <small class="text-muted">DepEd: <?php echo htmlspecialchars($employee['deped_id']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($employee['last_name'] . ', ' . $employee['first_name'] . ' ' . $employee['middle_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($employee['email_official']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($employee['position_title']); ?></td>
                            <td>SG <?php echo htmlspecialchars($employee['salary_grade']); ?></td>
                            <td><?php echo getStatusBadge($employee['employment_status']); ?></td>
                            <td><?php echo formatDate($employee['date_hired']); ?></td>
                            <td><?php echo $age; ?> years</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="view_employee.php?id=<?php echo $employee['employee_id']; ?>" 
                                       class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_employee.php?id=<?php echo $employee['employee_id']; ?>" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_employee.php?id=<?php echo $employee['employee_id']; ?>" 
                                       class="btn btn-danger btn-sm confirm-delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (count($employees) == 0): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No employees found matching your criteria.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>