<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>