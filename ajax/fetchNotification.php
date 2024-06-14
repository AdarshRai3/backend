<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Printing all headers received from the client
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

include_once 'connection.php';

// Check if the eid value is provided from the frontend
if (isset($_GET['eid'])) {
    $eid = $_GET['eid'];

    // Prepare the SQL statement
    $sql = "SELECT `notification` FROM `notifications` WHERE `eid` = ?";
    $stmt = $con->prepare($sql);

    // Bind the eid parameter
    $stmt->bind_param("i", $eid);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $data = array();

    if ($result->num_rows > 0) {
        // Fetch the data and store it in the $data array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        // If no rows are found, return an empty array
        $data = array();
    }

    // Close the statement and result set
    $stmt->close();
} else {
    // If eid is not provided, return an empty array
    $data = array();
}

if (ob_get_level()) {
    ob_end_clean();
}

// Encode the data array as JSON and output it
echo json_encode($data);

$con->close();
?>