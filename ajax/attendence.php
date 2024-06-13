<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, eid, `Name`, `Department`, `leave_type`, `start_date`, `end_date`, `reason` FROM `application`";
    $result = $con->query($sql);

    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        $data = array(
            array(
                "eid" => "",
                "name" => "",
                "department" => "",
            )
        );
    }

    ob_end_clean(); // Clear the output buffer
    echo json_encode($data);
    exit;
}

$result->close();
$con->close();
?>