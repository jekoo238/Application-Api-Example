<?php
require_once 'config.php';
$databaseManager = new DatabaseManager();
$arr = array();
if ($databaseManager->connect()) {
    $arr = array(
        "ok" => true,
        "code" => 0,
        "text" => "Connection success"
    );
} else {
    $arr = array(
        "ok" => false,
        "code" => 1,
        "text" => "Connection failed: " . mysqli_connect_error()
    );
}
echo json_encode($arr, JSON_PRETTY_PRINT);
?>