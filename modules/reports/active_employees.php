<?php
// modules/reports/active_employees.php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireRole('HR Staff');

$page_title = 'Active Employees Report';
require_once '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Get active employees with details
$query = "SELECT e.employee_id, e.deped_id, 
          CONCAT(e.last_name, ', ', e.first_name, ' ', COALESCE(e.middle_name, '')) as full_name,
          e.birth_date, e.gender, e.civil_status,
          ed.position_title, ed.salary_grade, ed.step_increment, ed.monthly_salary,
          ed.school_assignment, ed.employee_type,
          e.sss_no, e.pagibig_no, e.philhealth_no, e.gsis_no, e.tin_no,
          TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) as age,
          TIMESTAMPDIFF(YEAR, e.date_hired, CURDATE()) as years_of_service,
          DATE_ADD(e.birth_date, INTERVAL 65 YEAR) as retirement_date,
          DATEDIFF(DATE_ADD(e.birth_date, INTERVAL 65 YEAR), CURDATE()) as days_to_retirement
          FROM employees e
          LEFT JOIN employment_details ed ON e.employee_id = ed.employee_id
          WHERE e.employment_status = 'Active'
          ORDER BY e.last_name, e.first_name";

$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Active Employees Report</h6>
        <div>
            <button onclick="printReport()" class="btn btn-primary btn-sm">
                <i class="fas fa-print"></i> Print Report
            </button>
            <button onclick="exportToExcel()" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
        </div>
    </div>

    <div class="card-body">
        <!-- Report Filters -->
        <div class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">School Assignment</label>
                <select id="schoolFilter" class="form-control">
                    <option value="">All Schools</option>
                    <?php foreach ($silay_schools as $school): ?>
                        <option value="<?php echo htmlspecialchars($school); ?>"><?php echo $school; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Employee Type</label>
                <select id="typeFilter" class="form-control">
                    <option value="">All Types</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contractual">Contractual</option>
                    <option value="Substitute">Substitute</option>
                    <option value="Part-time">Part-time</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Salary Grade</label>
                <select id="gradeFilter" class="form-control">
                    <option value="">All Grades</option>
                    <?php for ($i = 1; $i <= 33; $i++): ?>
                        <option value="<?php echo $i; ?>">SG <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Retirement Within</label>
                <select id="retirementFilter" class="form-control">
                    <option value="">All</option>
                    <option value="30">30 days</option>
                    <option value="90">90 days</option>
                    <option value="180">6 months</option>
                    <option value="365">1 year</option>
                </select>
            </div>
        </div>

        <!-- Report Table -->
        <div class="table-responsive" id="reportTable">
            <table class="table table-bordered table-striped" id="employeeReport">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2">ID</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Position</th>
                        <th rowspan="2">School</th>
                        <th colspan="3" class="text-center">Employment Details</th>
                        <th colspan="5" class="text-center">Government IDs</th>
                        <th colspan="3" class="text-center">Retirement Information</th>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <th>SG/Step</th>
                        <th>Salary</th>
                        <th>GSIS</th>
                        <th>SSS</th>
                        <th>Pag-IBIG</th>
                        <th>PhilHealth</th>
                        <th>TIN</th>
                        <th>Age</th>
                        <th>Service</th>
                        <th>Retirement Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr data-school="<?php echo htmlspecialchars($employee['school_assignment']); ?>"
                            data-type="<?php echo htmlspecialchars($employee['employee_type']); ?>"
                            data-grade="<?php echo $employee['salary_grade']; ?>"
                            data-days="<?php echo $employee['days_to_retirement']; ?>">
                            <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['position_title']); ?></td>
                            <td><?php echo htmlspecialchars($employee['school_assignment']); ?></td>
                            <td><?php echo htmlspecialchars($employee['employee_type']); ?></td>
                            <td>SG <?php echo $employee['salary_grade']; ?>-<?php echo $employee['step_increment']; ?></td>
                            <td><?php echo formatCurrency($employee['monthly_salary']); ?></td>
                            <td><?php echo htmlspecialchars($employee['gsis_no']); ?></td>
                            <td><?php echo htmlspecialchars($employee['sss_no']); ?></td>
                            <td><?php echo htmlspecialchars($employee['pagibig_no']); ?></td>
                            <td><?php echo htmlspecialchars($employee['philhealth_no']); ?></td>
                            <td><?php echo htmlspecialchars($employee['tin_no']); ?></td>
                            <td><?php echo $employee['age']; ?></td>
                            <td><?php echo $employee['years_of_service']; ?> years</td>
                            <td>
                                <?php echo formatDate($employee['retirement_date']); ?>
                                <br>
                                <small class="text-muted">
                                    <?php
                                    $days = $employee['days_to_retirement'];
                                    if ($days <= 0) {
                                        echo '<span class="text-danger">Past Due</span>';
                                    } elseif ($days <= 30) {
                                        echo '<span class="text-warning">' . $days . ' days</span>';
                                    } elseif ($days <= 180) {
                                        echo '<span class="text-info">' . $days . ' days</span>';
                                    } else {
                                        echo $days . ' days';
                                    }
                                    ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Total Employees:</strong></td>
                        <td colspan="10"><strong><?php echo count($employees); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Employees</h5>
                        <h2 class="card-text"><?php echo count($employees); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Average Age</h5>
                        <h2 class="card-text">
                            <?php
                            $total_age = 0;
                            foreach ($employees as $emp) {
                                $total_age += $emp['age'];
                            }
                            echo count($employees) > 0 ? round($total_age / count($employees), 1) : 0;
                            ?> years
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Average Service</h5>
                        <h2 class="card-text">
                            <?php
                            $total_service = 0;
                            foreach ($employees as $emp) {
                                $total_service += $emp['years_of_service'];
                            }
                            echo count($employees) > 0 ? round($total_service / count($employees), 1) : 0;
                            ?> years
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Retiring in 6 Months</h5>
                        <h2 class="card-text">
                            <?php
                            $retiring_soon = 0;
                            foreach ($employees as $emp) {
                                if ($emp['days_to_retirement'] <= 180) {
                                    $retiring_soon++;
                                }
                            }
                            echo $retiring_soon;
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter table
    $(document).ready(function () {
        $('#schoolFilter, #typeFilter, #gradeFilter, #retirementFilter').change(function () {
            filterTable();
        });

        function filterTable() {
            const school = $('#schoolFilter').val();
            const type = $('#typeFilter').val();
            const grade = $('#gradeFilter').val();
            const retirement = $('#retirementFilter').val();

            $('#employeeReport tbody tr').each(function () {
                let show = true;

                if (school && $(this).data('school') !== school) {
                    show = false;
                }

                if (type && $(this).data('type') !== type) {
                    show = false;
                }

                if (grade && $(this).data('grade') != grade) {
                    show = false;
                }

                if (retirement && $(this).data('days') > retirement) {
                    show = false;
                }

                if (show) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    function printReport() {
        const printContent = document.getElementById('reportTable').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
        <html>
            <head>
                <title>Active Employees Report - <?php echo date('Y-m-d'); ?></title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; }
                    th { background-color: #f2f2f2; text-align: left; }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                </style>
            </head>
            <body>
                <h2>DepEd Silay City - Active Employees Report</h2>
                <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                ${printContent}
            </body>
        </html>
    `;

        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }

    function exportToExcel() {
        let table = document.getElementById('employeeReport');
        let html = table.outerHTML;

        // Create a Blob with the table data
        let blob = new Blob([html], { type: 'application/vnd.ms-excel' });

        // Create a link element
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'active_employees_report_<?php echo date('Y-m-d'); ?>.xls';

        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php
require_once '../../includes/footer.php';
?>