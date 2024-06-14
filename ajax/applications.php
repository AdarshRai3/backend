<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leaveType = isset($_POST['leave-type']) ? $_POST['leave-type'] : '';
    $startDate = isset($_POST['start-date']) ? $_POST['start-date'] : '';
    $endDate = isset($_POST['end-date']) ? $_POST['end-date'] : '';
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    $eid = isset($_POST['EID']) ? $_POST['EID'] : '';
    $name = isset($_POST['Name']) ? $_POST['Name'] : '';
    $department = isset($_POST['Department']) ? $_POST['Department'] : '';

    $sql = "INSERT INTO `application` (eid, name, department, leave_type, start_date, end_date, reason)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing the statement: " . $con->error;
    } else {
        $stmt->bind_param("issssss", $eid, $name, $department, $leaveType, $startDate, $endDate, $reason);
        if ($stmt->execute()) {
            echo "Leave application submitted successfully";
        } else {
            echo "Error submitting leave application: " . $stmt->error;
        }
        $stmt->close();
    }
}

$con->close();
?>