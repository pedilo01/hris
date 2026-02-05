<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check government_ids
    echo "--- CHECKING government_ids ---\n";
    $stmt = $db->query("DESCRIBE government_ids");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }

    $count = $db->query("SELECT COUNT(*) FROM government_ids")->fetchColumn();
    echo "\nRow count: $count\n";

    if ($count > 0) {
        $data = $db->query("SELECT * FROM government_ids LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        print_r($data);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>