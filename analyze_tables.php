<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Table Analysis:\n";
    echo sprintf("%-30s %-10s\n", "Table Name", "Rows");
    echo str_repeat("-", 40) . "\n";

    foreach ($tables as $table) {
        $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo sprintf("%-30s %-10d\n", $table, $count);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>