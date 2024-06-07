<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $eid = $input['eid'];
    $action = $input['action'];

    if ($action === 'Reconsider') {
        // Delete the employee from the offboarding table
        $sql = "DELETE FROM offboarding WHERE eid = ?";
        $message = "Employee reconsidered and removed from offboarding.";
    } else if ($action === 'Discharged') {
        // Delete the employee from the directory table
        $sql = "DELETE FROM directory WHERE eid = ?";
        $message = "Employee discharged and removed from directory.";
    } else {
        $response = array('status' => 'error', 'message' => 'Invalid action');
        echo json_encode($response);
        exit;
    }

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        $response = array('status' => 'error', 'message' => 'Prepare failed: ' . $con->error);
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("s", $eid);
    if (!$stmt->execute()) {
        $response = array('status' => 'error', 'message' => 'Execute failed: ' . $stmt->error);
    } else {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => $message);
        } else {
            $response = array('status' => 'warning', 'message' => "No rows were affected. Employee with ID $eid might not exist in the table.");
        }
    }

    $stmt->close();
    echo json_encode($response);
}

$con->close();
?>