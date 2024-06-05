<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eid = isset($_POST['eid']) ? $_POST['eid'] : '';
    $newStatus = isset($_POST['newStatus']) ? $_POST['newStatus'] : '';
    $clockInTime = isset($_POST['clockInTime']) ? $_POST['clockInTime'] : '';
    $clockOutTime = isset($_POST['clockOutTime']) ? $_POST['clockOutTime'] : '';
    $currentTimeValue = isset($_POST['currentTimeValue']) ? $_POST['currentTimeValue'] : '';
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : '';

    // Handle clock-out time
    if ($clockOutTime !== '00:00:00') {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $clockOutTime);
        $clockOutTime = $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
    }

    // Check if the record exists for the given EID
    $checkSql = "SELECT * FROM attendence WHERE eid = ?";
    $checkStmt = $con->prepare($checkSql);
    if ($checkStmt === false) {
        echo "Error preparing the statement: " . $con->error;
        exit;
    }
    $checkStmt->bind_param("i", $eid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists, fetch the row data
        $row = $result->fetch_assoc();
        $existingClockInTime = $row['CLOCK-IN Time'];

        if ($existingClockInTime !== "0000-00-00 00:00:00") {
            // Clock-in time already exists, update clock-out time, status, and time
            $sql = "UPDATE attendence SET `CLOCK-OUT Time` = ?, `status` = ?, `Time` = ?, `date` = ? WHERE eid = ?";
            $stmt = $con->prepare($sql);
            if ($stmt === false) {
                echo "Error preparing the statement: " . $con->error;
                exit;
            }
            $stmt->bind_param("ssssi", $clockOutTime, $newStatus, $currentTimeValue, $currentDate, $eid);
        } else {
            // Clock-in time doesn't exist, insert clock-in time, status, and time
            $sql = "UPDATE attendence SET `CLOCK-IN Time` = ?, `status` = ?, `Time` = ?, `date` = ? WHERE eid = ?";
            $stmt = $con->prepare($sql);
            if ($stmt === false) {
                echo "Error preparing the statement: " . $con->error;
                exit;
            }
            $stmt->bind_param("ssssi", $clockInTime, $newStatus, $currentTimeValue, $currentDate, $eid);
        }
    } else {
        // Record doesn't exist, insert a new record with clock-in time
        $sql = "INSERT INTO attendence (`eid`, `CLOCK-IN Time`, `status`, `Time`, `date`) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        if ($stmt === false) {
            echo "Error preparing the statement: " . $con->error;
            exit;
        }
        $stmt->bind_param("issss", $eid, $clockInTime, $newStatus, $currentTimeValue, $currentDate);
    }

    if ($stmt->execute()) {
        // Update the status in the directory table based on the EID
        $sql = "UPDATE directory SET `status` = ? WHERE eid = ?";
        $updateStmt = $con->prepare($sql);
        if ($updateStmt === false) {
            echo "Error preparing the statement: " . $con->error;
            exit;
        }
        $updateStmt->bind_param("si", $newStatus, $eid);
        $updateStmt->execute();
        $updateStmt->close();

        echo "Data updated successfully";
    } else {
        echo "Error updating data: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();