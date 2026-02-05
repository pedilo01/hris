<?php
// modules/employees/view_employee.php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireRole('HR Staff');

$page_title = 'View Employee';
require_once '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? clean_input($_GET['id']) : '';

if (empty($id)) {
    header('Location: list_employees.php');
    exit();
}

$stmt = $db->prepare("SELECT e.*, ed.* 
    FROM employees e 
    LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id 
    WHERE e.employee_id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Employee not found.");
}
?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Employee Details:
            <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
        </h6>
        <div>
            <a href="edit_employee.php?id=<?php echo $employee['employee_id']; ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="list_employees.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <?php if (!empty($employee['photo_path']) && file_exists(SITE_ROOT . '/assets/uploads/' . $employee['photo_path'])): ?>
                        <img src="<?php echo SITE_URL . 'assets/uploads/' . $employee['photo_path']; ?>"
                            class="img-fluid rounded-circle img-thumbnail"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto img-thumbnail"
                            style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-5x text-secondary"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h5>
                    <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
                </h5>
                <p class="text-muted mb-1">
                    <?php echo htmlspecialchars($employee['position_title']); ?>
                </p>
                <div class="mb-2">
                    <?php echo getStatusBadge($employee['employment_status']); ?>
                </div>
                <small class="text-muted">ID:
                    <?php echo htmlspecialchars($employee['employee_id']); ?>
                </small><br>
                <small class="text-muted">DepEd ID:
                    <?php echo htmlspecialchars($employee['deped_id']); ?>
                </small>
            </div>

            <div class="col-md-9">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal"
                            role="tab">Personal Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="employment-tab" data-bs-toggle="tab" href="#employment"
                            role="tab">Employment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="ids-tab" data-bs-toggle="tab" href="#ids" role="tab">Government IDs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab">Contact
                            Info</a>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 p-4 rounded-bottom" id="myTabContent">
                    <!-- Personal Info Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Full Name</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['first_name'] . ' ' . ($employee['middle_name'] ? $employee['middle_name'] . ' ' : '') . $employee['last_name'] . ($employee['suffix'] ? ' ' . $employee['suffix'] : '')); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Extension Name</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['extension_name'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Gender</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['gender']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Civil Status</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['civil_status']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Birth Date</div>
                            <div class="col-md-8">
                                <?php echo formatDate($employee['birth_date']); ?> (
                                <?php echo calculateAge($employee['birth_date']); ?> years old)
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Birth Place</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['birth_place']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Tab -->
                    <div class="tab-pane fade" id="employment" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Position Title</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['position_title']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Salary Grade / Step</div>
                            <div class="col-md-8">SG
                                <?php echo htmlspecialchars($employee['salary_grade']); ?> / Step
                                <?php echo htmlspecialchars($employee['step_increment']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Monthly Salary</div>
                            <div class="col-md-8">
                                <?php echo formatCurrency($employee['monthly_salary']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Employee Type</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['employee_type']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">School Assignment</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['school_assignment']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">School ID</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['school_id']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Division</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['division']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Date Hired</div>
                            <div class="col-md-8">
                                <?php echo formatDate($employee['date_hired']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Bank Info</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['bank_name']); ?><br>
                                Account:
                                <?php echo htmlspecialchars($employee['bank_account']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Government IDs Tab -->
                    <div class="tab-pane fade" id="ids" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">GSIS BP No.</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['gsis_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Pag-IBIG MID No.</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['pagibig_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">PhilHealth No.</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['philhealth_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">SSS No.</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['sss_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">TIN</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['tin_no'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">LRN Number</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['lrn_number'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info Tab -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Official Email</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['email_official']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Personal Email</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['email_personal']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Mobile Number</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['mobile_number']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Address</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['house_no_street'] . ', ' . $employee['barangay'] . ', ' . $employee['city'] . ', ' . $employee['province']); ?><br>
                                <?php echo htmlspecialchars($employee['zip_code']); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <hr>
                                <h6>Emergency Contact</h6>
                            </div>
                            <div class="col-md-4 fw-bold">Name</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['emergency_person']); ?> (
                                <?php echo htmlspecialchars($employee['emergency_relationship']); ?>)
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Contact</div>
                            <div class="col-md-8">
                                <?php echo htmlspecialchars($employee['emergency_contact']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>