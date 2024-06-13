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
        $sql = "DELETE FROM `application` WHERE id = ?";
        $message = "Application has been rejected";
        $notificationMessage = "Your leave application has been rejected for the period from $startDate to $endDate.";
    } else if ($action === 'approve') {
        $sql = "DELETE FROM `application` WHERE id = ?";
        $message = "Application has been accepted.";
        $notificationMessage = "Your leave application has been approved for the period from $startDate to $endDate.";
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
            // Insert the notification message into the 'notifications' table
            $notificationSql = "INSERT INTO notifications (eid, `notification`) VALUES (?, ?)";
            $notificationStmt = $con->prepare($notificationSql);
            if ($notificationStmt) {
                $notificationStmt->bind_param("ss", $eid, $notificationMessage);
                $notificationStmt->execute();
                $notificationStmt->close();
            }

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