<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "DROP TABLE IF EXISTS government_ids";
    $db->exec($sql);

    echo "Table 'government_ids' has been dropped successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>