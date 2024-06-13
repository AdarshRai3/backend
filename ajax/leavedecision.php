<?php
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once 'connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request body
    parse_str(file_get_contents("php://input"), $data);

    // Extract the required data
    $id = $data['id'] ?? null;
    $eid = $data['eid'] ?? null;
    $action = $data['action'] ?? null;

    // Validate the data
    if (!$id || !$eid || !$action) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Prepare the SQL statement for deleting from the applications table
    $sql = "DELETE FROM applications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the SQL statement
    if ($stmt->execute()) {
        // Delete successful
        // Prepare the notification message based on the action
        if ($action == "Approve") {
            $message = "Your application for leave has been approved.";
        } else {
            $message = "Your application for leave has been rejected.";
        }

        // Insert the notification into the notifications table
        $sql = "INSERT INTO notifications (`notification`, eid) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $message, $eid);
        if ($stmt->execute()) {
            // Close the statement
            $stmt->close();

            // Return a success response
            http_response_code(200);
            echo json_encode(["message" => $message]);
        } else {
            $error = $stmt->error;
            http_response_code(500);
            echo json_encode(["error" => "Error executing SQL statement: " . $error]);
            // Log or print the error message for debugging
            error_log("SQL Error: " . $error);
        }
    } else {
        // Delete failed
        $error = $stmt->error;
        http_response_code(500);
        echo json_encode(["error" => "Error executing SQL statement: " . $error]);
        // Log or print the error message for debugging
        error_log("SQL Error: " . $error);
    }

    // Close the database connection
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>