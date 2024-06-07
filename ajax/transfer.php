<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    $eid = $input['eid'];
    $department = $input['department'];
    $officeLocation = $input['office_location'];

    $sql = "UPDATE directory SET `Department` = ?, `Office_Location` = ? WHERE `eid` = ?";
    $stmt = $con->prepare($sql);

    if ($stmt !== false) {
        $stmt->bind_param("ssi", $department, $officeLocation, $eid);

        if ($stmt->execute()) {
            $response = array(
                'status' => 'success',
                'message' => 'Data updated successfully'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error updating data: ' . $stmt->error
            );
        }

        $stmt->close();
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error preparing the statement: ' . $con->error
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

$con->close();
?>