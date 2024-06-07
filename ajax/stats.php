<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

include_once 'connection.php';

// Check if the eid parameter is provided in the query string
if (isset($_GET['eid'])) {
    $eid = $_GET['eid'];

    // Fetch attendance and leave stats based on the provided eid
    $sql = "SELECT `Attendance`, `Leaves`, `EID` FROM stats WHERE `EID` = $eid";
    $result = $con->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data = array(
            "attendanceStats" => $row['Attendance'],
            "leaveStats" => $row['Leaves']
        );
        $result->close();
    } else {
        $data = array(
            "attendanceStats" => 0,
            "leaveStats" => 0
        );
    }

    $con->close();
    echo json_encode($data);
    exit;
} else {
    $data = array(
        "attendanceStats" => 0,
        "leaveStats" => 0
    );
    echo json_encode($data);
    exit;
}
?>