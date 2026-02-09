-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 03:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deped_silay_hris`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` varchar(50) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changes_made` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `employee_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `changes_made`, `ip_address`, `user_agent`, `browser`, `platform`, `timestamp`) VALUES
(1, 1, NULL, 'Logout', NULL, NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-02-05 00:51:53'),
(2, 1, NULL, 'Added new employee', 'employees', 'DEPED-SILAY-202602-8734', NULL, NULL, 'New employee record created', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 01:22:41'),
(3, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 01:32:49'),
(4, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 01:33:30'),
(5, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 02:26:34'),
(6, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 02:26:49'),
(7, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 02:27:24'),
(8, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-05 02:38:27'),
(9, 1, NULL, 'Logout', NULL, NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, NULL, '2026-02-05 02:54:23'),
(10, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-09 02:26:41'),
(11, 1, NULL, 'Updated employee record', 'employees', 'DEPED-SILAY-202602-8', NULL, NULL, 'Updated employee details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Chrome', 'Windows', '2026-02-09 02:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `dependents`
--

CREATE TABLE `dependents` (
  `dependent_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `relationship` enum('Child','Spouse','Parent','Sibling','Other') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `employer` varchar(200) DEFAULT NULL,
  `address` varchar(300) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `is_beneficiary` tinyint(1) DEFAULT 0,
  `percentage_share` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `document_type` enum('Appointment Paper','Service Record','Birth Certificate','Marriage Certificate','Diploma','Transcript','TOR','Certificate of Employment','Clearance','Performance Rating','Oath of Office','Ombudsman Clearance','NBI Clearance','Police Clearance','Medical Certificate','Others') DEFAULT NULL,
  `document_name` varchar(200) DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL,
  `date_uploaded` date DEFAULT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `educational_background`
--

CREATE TABLE `educational_background` (
  `education_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `level` enum('Elementary','Secondary','Vocational/Trade Course','College','Graduate Studies','Post Graduate') DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `degree_or_course` varchar(200) DEFAULT NULL,
  `year_graduated` year(4) DEFAULT NULL,
  `highest_grade_level` varchar(50) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `inclusive_dates_from` date DEFAULT NULL,
  `inclusive_dates_to` date DEFAULT NULL,
  `honors_received` varchar(200) DEFAULT NULL,
  `scholarship` varchar(200) DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(20) NOT NULL,
  `deped_id` varchar(20) DEFAULT NULL,
  `lrn_number` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `extension_name` varchar(20) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `birth_place` varchar(200) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed','Separated') DEFAULT NULL,
  `gsis_no` varchar(50) DEFAULT NULL,
  `pagibig_no` varchar(50) DEFAULT NULL,
  `philhealth_no` varchar(50) DEFAULT NULL,
  `sss_no` varchar(50) DEFAULT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `email_official` varchar(100) DEFAULT NULL,
  `email_personal` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `house_no_street` varchar(200) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT 'Silay City',
  `province` varchar(100) DEFAULT 'Negros Occidental',
  `zip_code` varchar(10) DEFAULT NULL,
  `emergency_person` varchar(200) DEFAULT NULL,
  `emergency_relationship` varchar(50) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `emergency_address` varchar(300) DEFAULT NULL,
  `employment_status` enum('Active','Retired','On-Leave','Resigned','AWOL','Deceased','Suspended') DEFAULT 'Active',
  `date_hired` date DEFAULT NULL,
  `date_retired` date DEFAULT NULL,
  `date_resigned` date DEFAULT NULL,
  `date_of_last_promotion` date DEFAULT NULL,
  `photo_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `deped_id`, `lrn_number`, `first_name`, `middle_name`, `last_name`, `suffix`, `extension_name`, `birth_date`, `birth_place`, `gender`, `civil_status`, `gsis_no`, `pagibig_no`, `philhealth_no`, `sss_no`, `tin_no`, `email_official`, `email_personal`, `mobile_number`, `telephone`, `house_no_street`, `barangay`, `city`, `province`, `zip_code`, `emergency_person`, `emergency_relationship`, `emergency_contact`, `emergency_address`, `employment_status`, `date_hired`, `date_retired`, `date_resigned`, `date_of_last_promotion`, `photo_path`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
('DEPED-SILAY-202602-8', 'JOHILO194', '3131313131', 'john lloyd', 'juno', 'pedilo', '', '', '1998-06-11', 'silay city', 'Male', 'Single', '32323232', '23232323', '323232323232', '2323232', '32323232323', 'ped@gmail.com', 'pedi@gmail.com', '43543543534', '53454535345', 'fssfdfsfdfsdfsdf', 'Guinhalaran', 'Silay City', 'Negros Occidental', '6116', 'ADADAD', 'ADADAS', '3132131231', 'SDADSADADADA', 'Active', '2019-03-13', NULL, NULL, NULL, NULL, '2026-02-05 01:22:41', '2026-02-09 02:29:29', 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `employment_details`
--

CREATE TABLE `employment_details` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `position_title` varchar(200) DEFAULT NULL,
  `salary_grade` int(11) DEFAULT NULL CHECK (`salary_grade` between 1 and 33),
  `step_increment` int(11) DEFAULT NULL CHECK (`step_increment` between 1 and 8),
  `monthly_salary` decimal(12,2) DEFAULT NULL,
  `plantilla_number` varchar(50) DEFAULT NULL,
  `item_number` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `school_assignment` varchar(200) DEFAULT NULL,
  `school_id` varchar(50) DEFAULT NULL,
  `school_district` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT 'Division of Silay City',
  `region` varchar(100) DEFAULT 'Region VI - Western Visayas',
  `employee_type` enum('Permanent','Contractual','Substitute','Part-time','Volunteer','CoS','Job Order') DEFAULT NULL,
  `appointment_status` enum('Original','Promoted','Reclassified','Re-employed') DEFAULT NULL,
  `appointment_nature` enum('Permanent','Temporary','Casual','Co-terminus') DEFAULT NULL,
  `date_of_last_promotion` date DEFAULT NULL,
  `date_of_last_step_increment` date DEFAULT NULL,
  `effectivity_date` date DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `payroll_account` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employment_details`
--

INSERT INTO `employment_details` (`id`, `employee_id`, `position_title`, `salary_grade`, `step_increment`, `monthly_salary`, `plantilla_number`, `item_number`, `department`, `school_assignment`, `school_id`, `school_district`, `division`, `region`, `employee_type`, `appointment_status`, `appointment_nature`, `date_of_last_promotion`, `date_of_last_step_increment`, `effectivity_date`, `bank_name`, `bank_account`, `payroll_account`) VALUES
(1, 'DEPED-SILAY-202602-8', 'AO II', 11, 3, 25159.10, '313213213', '312313123', 'FSDFSF', 'Silay National High School', '232321331', NULL, 'Division of Silay City', 'Region VI - Western Visayas', 'Permanent', 'Promoted', NULL, NULL, NULL, '2020-07-23', 'Land Bank of the Philippines', '313213123', '3123123123');

-- --------------------------------------------------------

--
-- Table structure for table `family_background`
--

CREATE TABLE `family_background` (
  `family_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `spouse_last_name` varchar(100) DEFAULT NULL,
  `spouse_first_name` varchar(100) DEFAULT NULL,
  `spouse_middle_name` varchar(100) DEFAULT NULL,
  `spouse_occupation` varchar(100) DEFAULT NULL,
  `spouse_employer` varchar(200) DEFAULT NULL,
  `spouse_business_address` varchar(300) DEFAULT NULL,
  `spouse_telephone` varchar(20) DEFAULT NULL,
  `spouse_mobile` varchar(20) DEFAULT NULL,
  `father_last_name` varchar(100) DEFAULT NULL,
  `father_first_name` varchar(100) DEFAULT NULL,
  `father_middle_name` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_employer` varchar(200) DEFAULT NULL,
  `mother_last_name` varchar(100) DEFAULT NULL,
  `mother_first_name` varchar(100) DEFAULT NULL,
  `mother_middle_name` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_employer` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `government_ids`
--

CREATE TABLE `government_ids` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `sss_number` varchar(20) DEFAULT NULL,
  `sss_date_issued` date DEFAULT NULL,
  `sss_date_coverage` date DEFAULT NULL,
  `sss_upload` varchar(500) DEFAULT NULL,
  `pagibig_number` varchar(20) DEFAULT NULL,
  `pagibig_date_issued` date DEFAULT NULL,
  `pagibig_upload` varchar(500) DEFAULT NULL,
  `philhealth_number` varchar(20) DEFAULT NULL,
  `philhealth_date_issued` date DEFAULT NULL,
  `philhealth_category` varchar(50) DEFAULT NULL,
  `philhealth_upload` varchar(500) DEFAULT NULL,
  `tin_number` varchar(20) DEFAULT NULL,
  `tin_date_issued` date DEFAULT NULL,
  `tin_upload` varchar(500) DEFAULT NULL,
  `gsis_number` varchar(20) DEFAULT NULL,
  `gsis_bp_number` varchar(20) DEFAULT NULL,
  `gsis_date_issued` date DEFAULT NULL,
  `gsis_upload` varchar(500) DEFAULT NULL,
  `prc_license` varchar(50) DEFAULT NULL,
  `prc_expiry` date DEFAULT NULL,
  `prc_upload` varchar(500) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL,
  `passport_expiry` date DEFAULT NULL,
  `passport_upload` varchar(500) DEFAULT NULL,
  `drivers_license` varchar(50) DEFAULT NULL,
  `dl_expiry` date DEFAULT NULL,
  `umid_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_credits`
--

CREATE TABLE `leave_credits` (
  `credit_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `vacation_leave_earned` decimal(5,2) DEFAULT 0.00,
  `vacation_leave_used` decimal(5,2) DEFAULT 0.00,
  `vacation_leave_balance` decimal(5,2) DEFAULT 0.00,
  `sick_leave_earned` decimal(5,2) DEFAULT 0.00,
  `sick_leave_used` decimal(5,2) DEFAULT 0.00,
  `sick_leave_balance` decimal(5,2) DEFAULT 0.00,
  `force_leave` decimal(5,2) DEFAULT 0.00,
  `special_leave` decimal(5,2) DEFAULT 0.00,
  `total_leave_balance` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_records`
--

CREATE TABLE `leave_records` (
  `leave_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `leave_type` enum('Vacation','Sick','Maternity','Paternity','Special Privilege','Mandatory','Study','Rehabilitation','Monetization') DEFAULT NULL,
  `date_filed` date DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `number_of_days` decimal(5,2) DEFAULT NULL,
  `inclusive_dates` text DEFAULT NULL,
  `status` enum('Pending','Approved','Denied','Cancelled','Availed') DEFAULT 'Pending',
  `approved_by` varchar(100) DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `disapproved_by` varchar(100) DEFAULT NULL,
  `date_disapproved` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `balance_after` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retirement_alerts`
--

CREATE TABLE `retirement_alerts` (
  `alert_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `alert_type` enum('Retirement Due','Document Expiry','Requirement Missing','Birthday','Service Milestone') DEFAULT NULL,
  `alert_message` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `days_remaining` int(11) DEFAULT NULL,
  `status` enum('Unread','Read','Acted Upon','Dismissed') DEFAULT 'Unread',
  `priority` enum('High','Medium','Low') DEFAULT 'Medium',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `acted_date` date DEFAULT NULL,
  `acted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retirement_checklist`
--

CREATE TABLE `retirement_checklist` (
  `checklist_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `requirement_category` varchar(100) DEFAULT NULL,
  `requirement_item` varchar(200) DEFAULT NULL,
  `status` enum('Pending','Submitted','Verified','Rejected') DEFAULT 'Pending',
  `date_required` date DEFAULT NULL,
  `date_submitted` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retirement_info`
--

CREATE TABLE `retirement_info` (
  `retirement_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `retirement_status` enum('Not Yet Eligible','Eligible','Applied','Approved','Processed','Denied') DEFAULT 'Not Yet Eligible',
  `retirement_type` enum('Optional','Compulsory','Total Disability','Survivorship','Early') DEFAULT NULL,
  `date_of_retirement` date DEFAULT NULL,
  `date_of_application` date DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `date_processed` date DEFAULT NULL,
  `years_in_service` int(11) DEFAULT NULL,
  `months_in_service` int(11) DEFAULT NULL,
  `age_at_retirement` int(11) DEFAULT NULL,
  `gsis_retirement_claim_number` varchar(50) DEFAULT NULL,
  `terminal_leave_benefits` decimal(12,2) DEFAULT NULL,
  `last_salary_rate` decimal(12,2) DEFAULT NULL,
  `monthly_pension` decimal(12,2) DEFAULT NULL,
  `lump_sum_amount` decimal(12,2) DEFAULT NULL,
  `requirements_status` enum('Complete','Incomplete','Pending') DEFAULT 'Pending',
  `clearance_status` enum('Pending','Cleared','With Issues') DEFAULT 'Pending',
  `property_clearance` tinyint(1) DEFAULT 0,
  `financial_clearance` tinyint(1) DEFAULT 0,
  `application_form_path` varchar(500) DEFAULT NULL,
  `service_record_path` varchar(500) DEFAULT NULL,
  `clearance_form_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_records`
--

CREATE TABLE `service_records` (
  `record_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `sequence_number` int(11) DEFAULT NULL,
  `position` varchar(200) DEFAULT NULL,
  `appointment_status` varchar(100) DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `salary_grade` int(11) DEFAULT NULL,
  `step_increment` int(11) DEFAULT NULL,
  `station_or_place_of_assignment` varchar(200) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `period_from` date DEFAULT NULL,
  `period_to` date DEFAULT NULL,
  `length_of_service_years` int(11) DEFAULT NULL,
  `length_of_service_months` int(11) DEFAULT NULL,
  `length_of_service_days` int(11) DEFAULT NULL,
  `leave_without_pay_years` int(11) DEFAULT 0,
  `leave_without_pay_months` int(11) DEFAULT 0,
  `leave_without_pay_days` int(11) DEFAULT 0,
  `separation_date` date DEFAULT NULL,
  `separation_cause` varchar(200) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `record_upload` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `training_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `training_title` varchar(300) DEFAULT NULL,
  `training_type` enum('Local','National','International','Online','In-service') DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `conducted_by` varchar(200) DEFAULT NULL,
  `venue` varchar(200) DEFAULT NULL,
  `address` varchar(300) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `certificate_number` varchar(100) DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL,
  `sponsor` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `employee_id` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_role` enum('Super Admin','HR Admin','HR Staff','Supervisor','Employee','View Only') DEFAULT 'Employee',
  `department_access` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lock_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `employee_id`, `username`, `password_hash`, `email`, `user_role`, `department_access`, `is_active`, `last_login`, `login_attempts`, `lock_until`, `created_at`, `created_by`) VALUES
(1, NULL, 'admin', '$2y$10$c5GUcswAmu/iieHIGt6xVeEy48gZ4S4rA7Wt7w/rScGov5bdpvDoC', 'admin@depedsilay.gov.ph', 'Super Admin', NULL, 1, '2026-02-09 09:36:43', 0, NULL, '2026-02-05 00:23:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_action_timestamp` (`action`,`timestamp`),
  ADD KEY `idx_employee_action` (`employee_id`,`action`);

--
-- Indexes for table `dependents`
--
ALTER TABLE `dependents`
  ADD PRIMARY KEY (`dependent_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_relationship` (`relationship`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indexes for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD PRIMARY KEY (`education_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_education_level` (`level`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `deped_id` (`deped_id`),
  ADD KEY `idx_last_name` (`last_name`),
  ADD KEY `idx_employment_status` (`employment_status`),
  ADD KEY `idx_deped_id` (`deped_id`);

--
-- Indexes for table `employment_details`
--
ALTER TABLE `employment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_position` (`position_title`),
  ADD KEY `idx_school` (`school_assignment`);

--
-- Indexes for table `family_background`
--
ALTER TABLE `family_background`
  ADD PRIMARY KEY (`family_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `government_ids`
--
ALTER TABLE `government_ids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_govt` (`employee_id`),
  ADD KEY `idx_sss` (`sss_number`),
  ADD KEY `idx_gsis` (`gsis_number`);

--
-- Indexes for table `leave_credits`
--
ALTER TABLE `leave_credits`
  ADD PRIMARY KEY (`credit_id`),
  ADD UNIQUE KEY `unique_employee_year` (`employee_id`,`year`);

--
-- Indexes for table `leave_records`
--
ALTER TABLE `leave_records`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_leave_dates` (`date_from`,`date_to`),
  ADD KEY `idx_leave_status` (`status`);

--
-- Indexes for table `retirement_alerts`
--
ALTER TABLE `retirement_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_alert_status` (`status`),
  ADD KEY `idx_alert_due` (`due_date`);

--
-- Indexes for table `retirement_checklist`
--
ALTER TABLE `retirement_checklist`
  ADD PRIMARY KEY (`checklist_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `retirement_info`
--
ALTER TABLE `retirement_info`
  ADD PRIMARY KEY (`retirement_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_retirement_status` (`retirement_status`);

--
-- Indexes for table `service_records`
--
ALTER TABLE `service_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_service_period` (`period_from`,`period_to`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`training_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `idx_training_dates` (`date_from`,`date_to`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dependents`
--
ALTER TABLE `dependents`
  MODIFY `dependent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `educational_background`
--
ALTER TABLE `educational_background`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_details`
--
ALTER TABLE `employment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `family_background`
--
ALTER TABLE `family_background`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `government_ids`
--
ALTER TABLE `government_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_credits`
--
ALTER TABLE `leave_credits`
  MODIFY `credit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_records`
--
ALTER TABLE `leave_records`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retirement_alerts`
--
ALTER TABLE `retirement_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retirement_checklist`
--
ALTER TABLE `retirement_checklist`
  MODIFY `checklist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retirement_info`
--
ALTER TABLE `retirement_info`
  MODIFY `retirement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_records`
--
ALTER TABLE `service_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `training_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dependents`
--
ALTER TABLE `dependents`
  ADD CONSTRAINT `dependents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `educational_background`
--
ALTER TABLE `educational_background`
  ADD CONSTRAINT `educational_background_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `employment_details`
--
ALTER TABLE `employment_details`
  ADD CONSTRAINT `employment_details_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `family_background`
--
ALTER TABLE `family_background`
  ADD CONSTRAINT `family_background_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `government_ids`
--
ALTER TABLE `government_ids`
  ADD CONSTRAINT `government_ids_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_credits`
--
ALTER TABLE `leave_credits`
  ADD CONSTRAINT `leave_credits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_records`
--
ALTER TABLE `leave_records`
  ADD CONSTRAINT `leave_records_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `retirement_alerts`
--
ALTER TABLE `retirement_alerts`
  ADD CONSTRAINT `retirement_alerts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `retirement_checklist`
--
ALTER TABLE `retirement_checklist`
  ADD CONSTRAINT `retirement_checklist_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `retirement_info`
--
ALTER TABLE `retirement_info`
  ADD CONSTRAINT `retirement_info_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_records`
--
ALTER TABLE `service_records`
  ADD CONSTRAINT `service_records_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainings`
--
ALTER TABLE `trainings`
  ADD CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
