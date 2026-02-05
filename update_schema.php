<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Add columns if they don't exist
    $columns_to_add = [
        'gsis_no' => 'VARCHAR(50) DEFAULT NULL AFTER civil_status',
        'pagibig_no' => 'VARCHAR(50) DEFAULT NULL AFTER gsis_no',
        'philhealth_no' => 'VARCHAR(50) DEFAULT NULL AFTER pagibig_no',
        'sss_no' => 'VARCHAR(50) DEFAULT NULL AFTER philhealth_no',
        'tin_no' => 'VARCHAR(50) DEFAULT NULL AFTER sss_no'
    ];

    foreach ($columns_to_add as $column => $definition) {
        try {
            // Check if column exists
            $check = $db->query("SHOW COLUMNS FROM employees LIKE '$column'");
            if ($check->rowCount() == 0) {
                // Add column
                $sql = "ALTER TABLE employees ADD COLUMN $column $definition";
                $db->exec($sql);
                echo "Added column: $column\n";
            } else {
                echo "Column already exists: $column\n";
            }
        } catch (PDOException $e) {
            echo "Error adding $column: " . $e->getMessage() . "\n";
        }
    }

    echo "Schema update completed.\n";

} catch (PDOException $e) {
    echo "Connection Error: " . $e->getMessage();
}
?>