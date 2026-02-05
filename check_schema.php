<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check columns in specific tables
    $tables = ['employees', 'employment_details'];

    foreach ($tables as $table) {
        $stmt = $db->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Columns in $table:\n";
        echo implode(", ", $columns) . "\n\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>