<?php
$userApiKey = $_POST['api_key'];
$apiKey = "Alza01";
if ($userApiKey !== $apiKey) {
    $arr = array(
        "ok" => false,
        "code" => 2,
        "text" => "Forbidden"
    );
    echo json_encode($arr, JSON_PRETTY_PRINT);
    exit;
}
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

//close request
$databaseManager->close();
exit;
?>