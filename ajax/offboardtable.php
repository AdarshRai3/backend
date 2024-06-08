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
    // Get the JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check for valid JSON data
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"]);
        exit;
    }

    // Extract employee data
    $eid = $data['eid'] ?? null;
    $name = $data['name'] ?? null;
    $jobTitle = $data['jobTitle'] ?? null;
    $department = $data['department'] ?? null;
    $salary = $data['salary'] ?? null;

    // Validate the data
    if (!$eid || !$name || !$jobTitle || !$department || !$salary) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Prepare the SQL statement
    $stmt = $con->prepare("INSERT INTO offboarding (eid, name, `Job Title`, department, salary) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $con->error]);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param("sssss", $eid, $name, $jobTitle, $department, $salary);
    if ($stmt->execute()) {
        http_response_code(201); // 201 Created
        echo json_encode(["message" => "Employee offboarded successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
$con->close();
?>