<?php
require_once "./config/dbConnection.php";
$database = new Database();
$conn = $database->connect();

$sql = "TRUNCATE TABLE dice_history";
$stmt = $conn->prepare($sql);
$stmt->execute();
echo "Table 'dice_history' truncated successfully.";
