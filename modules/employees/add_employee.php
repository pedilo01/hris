<?php
// modules/employees/add_employee.php
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
requireRole('HR Staff');

$page_title = 'Add New Employee';
require_once '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        // Generate IDs
        $employee_id = $database->generateEmployeeID();
        $deped_id = $database->generateDepEdID($_POST['first_name'], $_POST['last_name']);
        
        // Insert into employees table
        $employee_query = "INSERT INTO employees (
            employee_id, deped_id, lrn_number,
            first_name, middle_name, last_name, suffix, extension_name,
            birth_date, birth_place, gender, civil_status,
            gsis_no, pagibig_no, philhealth_no, sss_no, tin_no,
            email_official, email_personal, mobile_number, telephone,
            house_no_street, barangay, city, province, zip_code,
            emergency_person, emergency_relationship, emergency_contact, emergency_address,
            employment_status, date_hired, created_by
        ) VALUES (
            :employee_id, :deped_id, :lrn_number,
            :first_name, :middle_name, :last_name, :suffix, :extension_name,
            :birth_date, :birth_place, :gender, :civil_status,
            :gsis_no, :pagibig_no, :philhealth_no, :sss_no, :tin_no,
            :email_official, :email_personal, :mobile_number, :telephone,
            :house_no_street, :barangay, :city, :province, :zip_code,
            :emergency_person, :emergency_relationship, :emergency_contact, :emergency_address,
            :employment_status, :date_hired, :created_by
        )";
        
        $employee_stmt = $db->prepare($employee_query);
        
        $employee_stmt->bindParam(':employee_id', $employee_id);
        $employee_stmt->bindParam(':deped_id', $deped_id);
        $employee_stmt->bindParam(':lrn_number', $_POST['lrn_number']);
        $employee_stmt->bindParam(':first_name', $_POST['first_name']);
        $employee_stmt->bindParam(':middle_name', $_POST['middle_name']);
        $employee_stmt->bindParam(':last_name', $_POST['last_name']);
        $employee_stmt->bindParam(':suffix', $_POST['suffix']);
        $employee_stmt->bindParam(':extension_name', $_POST['extension_name']);
        $employee_stmt->bindParam(':birth_date', $_POST['birth_date']);
        $employee_stmt->bindParam(':birth_place', $_POST['birth_place']);
        $employee_stmt->bindParam(':gender', $_POST['gender']);
        $employee_stmt->bindParam(':civil_status', $_POST['civil_status']);
        $employee_stmt->bindParam(':gsis_no', $_POST['gsis_no']);
        $employee_stmt->bindParam(':pagibig_no', $_POST['pagibig_no']);
        $employee_stmt->bindParam(':philhealth_no', $_POST['philhealth_no']);
        $employee_stmt->bindParam(':sss_no', $_POST['sss_no']);
        $employee_stmt->bindParam(':tin_no', $_POST['tin_no']);
        $employee_stmt->bindParam(':email_official', $_POST['email_official']);
        $employee_stmt->bindParam(':email_personal', $_POST['email_personal']);
        $employee_stmt->bindParam(':mobile_number', $_POST['mobile_number']);
        $employee_stmt->bindParam(':telephone', $_POST['telephone']);
        $employee_stmt->bindParam(':house_no_street', $_POST['house_no_street']);
        $employee_stmt->bindParam(':barangay', $_POST['barangay']);
        $employee_stmt->bindParam(':city', $_POST['city']);
        $employee_stmt->bindParam(':province', $_POST['province']);
        $employee_stmt->bindParam(':zip_code', $_POST['zip_code']);
        $employee_stmt->bindParam(':emergency_person', $_POST['emergency_person']);
        $employee_stmt->bindParam(':emergency_relationship', $_POST['emergency_relationship']);
        $employee_stmt->bindParam(':emergency_contact', $_POST['emergency_contact']);
        $employee_stmt->bindParam(':emergency_address', $_POST['emergency_address']);
        $employee_stmt->bindParam(':employment_status', $_POST['employment_status']);
        $employee_stmt->bindParam(':date_hired', $_POST['date_hired']);
        $employee_stmt->bindParam(':created_by', $_SESSION['username']);
        
        $employee_stmt->execute();
        
        // Insert into employment_details table
        $employment_query = "INSERT INTO employment_details (
            employee_id, position_title, salary_grade, step_increment, monthly_salary,
            plantilla_number, item_number, department, school_assignment, school_id,
            school_district, division, region, employee_type, appointment_status,
            appointment_nature, effectivity_date, bank_name, bank_account, payroll_account
        ) VALUES (
            :employee_id, :position_title, :salary_grade, :step_increment, :monthly_salary,
            :plantilla_number, :item_number, :department, :school_assignment, :school_id,
            :school_district, :division, :region, :employee_type, :appointment_status,
            :appointment_nature, :effectivity_date, :bank_name, :bank_account, :payroll_account
        )";
        
        $employment_stmt = $db->prepare($employment_query);
        
        $employment_stmt->bindParam(':employee_id', $employee_id);
        $employment_stmt->bindParam(':position_title', $_POST['position_title']);
        $employment_stmt->bindParam(':salary_grade', $_POST['salary_grade']);
        $employment_stmt->bindParam(':step_increment', $_POST['step_increment']);
        $employment_stmt->bindParam(':monthly_salary', $_POST['monthly_salary']);
        $employment_stmt->bindParam(':plantilla_number', $_POST['plantilla_number']);
        $employment_stmt->bindParam(':item_number', $_POST['item_number']);
        $employment_stmt->bindParam(':department', $_POST['department']);
        $employment_stmt->bindParam(':school_assignment', $_POST['school_assignment']);
        $employment_stmt->bindParam(':school_id', $_POST['school_id']);
        $employment_stmt->bindParam(':school_district', $_POST['school_district']);
        $employment_stmt->bindParam(':division', $_POST['division']);
        $employment_stmt->bindParam(':region', $_POST['region']);
        $employment_stmt->bindParam(':employee_type', $_POST['employee_type']);
        $employment_stmt->bindParam(':appointment_status', $_POST['appointment_status']);
        $employment_stmt->bindParam(':appointment_nature', $_POST['appointment_nature']);
        $employment_stmt->bindParam(':effectivity_date', $_POST['effectivity_date']);
        $employment_stmt->bindParam(':bank_name', $_POST['bank_name']);
        $employment_stmt->bindParam(':bank_account', $_POST['bank_account']);
        $employment_stmt->bindParam(':payroll_account', $_POST['payroll_account']);
        
        $employment_stmt->execute();
        
        // Log the activity
        logActivity('Added new employee', 'employees', $employee_id, 'New employee record created');
        
        $db->commit();
        
        $success = "Employee added successfully! Employee ID: $employee_id, DepEd ID: $deped_id";
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error adding employee: " . $e->getMessage();
    }
}
?>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add New Employee</h6>
    </div>
    
    <div class="card-body">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Suffix (Jr., III, etc.)</label>
                            <select name="suffix" class="form-control">
                                <option value="">None</option>
                                <option value="Jr.">Jr.</option>
                                <option value="Sr.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Extension Name</label>
                            <input type="text" name="extension_name" class="form-control" placeholder="e.g., Dela Cruz">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Place of Birth <span class="text-danger">*</span></label>
                            <input type="text" name="birth_place" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Government IDs -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-id-card"></i> Government IDs</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">GSIS BP No.</label>
                            <input type="text" name="gsis_no" class="form-control" placeholder="0000000000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pag-IBIG MID No.</label>
                            <input type="text" name="pagibig_no" class="form-control" placeholder="0000-0000-0000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">PhilHealth No.</label>
                            <input type="text" name="philhealth_no" class="form-control" placeholder="00-000000000-0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SSS No.</label>
                            <input type="text" name="sss_no" class="form-control" placeholder="00-0000000-0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">TIN</label>
                            <input type="text" name="tin_no" class="form-control" placeholder="000-000-000-000">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-address-book"></i> Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Official Email <span class="text-danger">*</span></label>
                            <input type="email" name="email_official" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Personal Email</label>
                            <input type="email" name="email_personal" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" name="mobile_number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telephone Number</label>
                            <input type="tel" name="telephone" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Complete Address <span class="text-danger">*</span></label>
                            <input type="text" name="house_no_street" class="form-control" placeholder="House No. & Street" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay" class="form-control" required>
                                <option value="">Select Barangay</option>
                                <?php foreach ($silay_barangays as $barangay): ?>
                                    <option value="<?php echo $barangay; ?>"><?php echo $barangay; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" value="Silay City" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Province <span class="text-danger">*</span></label>
                            <input type="text" name="province" class="form-control" value="Negros Occidental" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" value="6116">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">LRN Number</label>
                            <input type="text" name="lrn_number" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Employment Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-briefcase"></i> Employment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Position Title <span class="text-danger">*</span></label>
                            <input type="text" name="position_title" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Salary Grade <span class="text-danger">*</span></label>
                            <select name="salary_grade" class="form-control" required>
                                <option value="">Select SG</option>
                                <?php for ($i = 1; $i <= 33; $i++): ?>
                                    <option value="<?php echo $i; ?>">SG <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Step Increment <span class="text-danger">*</span></label>
                            <select name="step_increment" class="form-control" required>
                                <option value="">Select Step</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?php echo $i; ?>">Step <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Monthly Salary <span class="text-danger">*</span></label>
                            <input type="number" name="monthly_salary" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Employee Type <span class="text-danger">*</span></label>
                            <select name="employee_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Substitute">Substitute</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Volunteer">Volunteer</option>
                                <option value="CoS">Contract of Service</option>
                                <option value="Job Order">Job Order</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Employment Status <span class="text-danger">*</span></label>
                            <select name="employment_status" class="form-control" required>
                                <option value="Active" selected>Active</option>
                                <option value="On-Leave">On-Leave</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date Hired <span class="text-danger">*</span></label>
                            <input type="date" name="date_hired" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Effectivity Date</label>
                            <input type="date" name="effectivity_date" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Appointment Status</label>
                            <select name="appointment_status" class="form-control">
                                <option value="Original">Original</option>
                                <option value="Promoted">Promoted</option>
                                <option value="Reclassified">Reclassified</option>
                                <option value="Re-employed">Re-employed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">School Assignment <span class="text-danger">*</span></label>
                            <select name="school_assignment" class="form-control" required>
                                <option value="">Select School</option>
                                <?php foreach ($silay_schools as $code => $school): ?>
                                    <option value="<?php echo $school; ?>"><?php echo $school; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">School ID</label>
                            <input type="text" name="school_id" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Plantilla Number</label>
                            <input type="text" name="plantilla_number" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Item Number</label>
                            <input type="text" name="item_number" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Division</label>
                            <input type="text" name="division" class="form-control" value="Division of Silay City" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Region</label>
                            <input type="text" name="region" class="form-control" value="Region VI - Western Visayas" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bank Information -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-university"></i> Bank Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bank Name</label>
                            <select name="bank_name" class="form-control">
                                <option value="">Select Bank</option>
                                <option value="Land Bank of the Philippines">Land Bank of the Philippines</option>
                                <option value="Development Bank of the Philippines">DBP</option>
                                <option value="Philippine National Bank">PNB</option>
                                <option value="Bank of the Philippine Islands">BPI</option>
                                <option value="Metrobank">Metrobank</option>
                                <option value="BDO">BDO</option>
                                <option value="Union Bank">Union Bank</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bank Account Number</label>
                            <input type="text" name="bank_account" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Payroll Account</label>
                            <input type="text" name="payroll_account" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Contact -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-phone-alt"></i> Emergency Contact</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Emergency Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_person" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_relationship" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Emergency Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" name="emergency_contact" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Emergency Address</label>
                            <input type="text" name="emergency_address" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Employee Record
                </button>
                <a href="list_employees.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Calculate salary based on salary grade and step
$(document).ready(function() {
    const salaryMatrix = <?php echo json_encode($salary_grades); ?>;
    const stepIncrement = 0.03; // 3% per step
    
    $('select[name="salary_grade"], select[name="step_increment"]').change(function() {
        const salaryGrade = $('select[name="salary_grade"]').val();
        const step = $('select[name="step_increment"]').val();
        
        if (salaryGrade && step) {
            let baseSalary = salaryMatrix[salaryGrade];
            let calculatedSalary = baseSalary * (1 + ((step - 1) * stepIncrement));
            $('input[name="monthly_salary"]').val(calculatedSalary.toFixed(2));
        }
    });
});
</script>

<?php
require_once '../../includes/footer.php';
?>