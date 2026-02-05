<?php
// config/constants.php

define('SITE_NAME', 'DepEd Silay HRIS System');
define('SITE_URL', 'http://localhost/hris/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/hris/assets/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// User Roles
define('ROLE_SUPER_ADMIN', 'Super Admin');
define('ROLE_HR_ADMIN', 'HR Admin');
define('ROLE_HR_STAFF', 'HR Staff');
define('ROLE_SUPERVISOR', 'Supervisor');
define('ROLE_EMPLOYEE', 'Employee');
define('ROLE_VIEW_ONLY', 'View Only');

// Employment Status
define('STATUS_ACTIVE', 'Active');
define('STATUS_RETIRED', 'Retired');
define('STATUS_ON_LEAVE', 'On-Leave');
define('STATUS_RESIGNED', 'Resigned');
define('STATUS_AWOL', 'AWOL');
define('STATUS_DECEASED', 'Deceased');

// Retirement Status
define('RETIREMENT_NOT_ELIGIBLE', 'Not Yet Eligible');
define('RETIREMENT_ELIGIBLE', 'Eligible');
define('RETIREMENT_APPLIED', 'Applied');
define('RETIREMENT_APPROVED', 'Approved');
define('RETIREMENT_PROCESSED', 'Processed');

// Salary Grades (DepEd Standard)
$salary_grades = array(
    1 => 13000,
    2 => 13819,
    3 => 14678,
    4 => 15589,
    5 => 16556,
    6 => 17582,
    7 => 18670,
    8 => 19825,
    9 => 21051,
    10 => 22353,
    11 => 23735,
    12 => 25203,
    13 => 26762,
    14 => 28419,
    15 => 30182,
    16 => 32057,
    17 => 34053,
    18 => 36179,
    19 => 38445,
    20 => 40861,
    21 => 43439,
    22 => 46191,
    23 => 49130,
    24 => 52271,
    25 => 55630,
    26 => 59222,
    27 => 63066,
    28 => 67181,
    29 => 71586,
    30 => 76304,
    31 => 81357,
    32 => 86771,
    33 => 92573
);

// DepEd Silay Schools
$silay_schools = array(
    'SDO001' => 'Silay National High School',
    'SDO002' => 'Silay Institute',
    'SDO003' => 'Doña Montserrat Lopez Memorial High School',
    'SDO004' => 'E. B. Magalona National High School',
    'SDO005' => 'Silay City Elementary School',
    'SDO006' => 'Rizal Elementary School',
    'SDO007' => 'Mambulac Elementary School',
    'SDO008' => 'E. Lopez Elementary School',
    'SDO009' => 'Kapitan Ramon Elementary School',
    'SDO010' => 'Silay City SPED Center'
);

// Barangays in Silay City
$silay_barangays = array(
    'Bagtic',
    'Balaring',
    'Barangay I',
    'Barangay II',
    'Barangay III',
    'Barangay IV',
    'Barangay V',
    'Barangay VI',
    'E. Lopez',
    'Guimbala-on',
    'Guinhalaran',
    'Kapitan Ramon',
    'Lantad',
    'Mambulac',
    'Rizal',
    'Sagay',
    'Patag',
    'Punta Salong',
    'Tubuangan'
);
?>