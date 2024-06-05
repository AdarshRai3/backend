<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

include_once 'connection.php';

$sql = "SELECT eid, `Name`, `Job Title` AS position, `Department`, `Office_Location` AS office_location, `Status` AS status, `Phone`, `Email` FROM directory";
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
            "position" => "",
            "department" => "",
            "office_location" => "",
            "status" => "",
            "phone" => "",
            "email" => ""
        )
    );
}

if (ob_get_level()) {
    ob_end_clean();
}

echo json_encode($data);

$result->close();
$con->close();
?>