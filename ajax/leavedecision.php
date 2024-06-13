<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    
    $id = $input['id'];
    $eid = $input['eid'];
    $action = $input['action'];
    $startDate = $input['startDate'];
    $endDate = $input['endDate'];

    if ($action === 'reject') {
        // Delete the employee from the offboarding table
        $sql = "DELETE FROM `application` WHERE id = ?";
        $message = "Application has been rejected";
    } else if ($action === 'approve') {
        // Delete the employee from the directory table
        $sql = "DELETE FROM `application` WHERE id = ?";
        $message = "Application has been accepted.";
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

    $stmt->bind_param("s", $id);
    if (!$stmt->execute()) {
        $response = array('status' => 'error', 'message' => 'Execute failed: ' . $stmt->error);
    } else {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => $message);
        } else {
            $response = array('status' => 'warning', 'message' => "No rows were affected. Employee with ID $id might not exist in the table.");
        }
    }

    $stmt->close();
    echo json_encode($response);
}

$con->close();
?>