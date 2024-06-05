<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type");
include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $jobTitle = isset($_POST['job-title']) ? $_POST['job-title'] : '';
    $department = isset($_POST['department']) ? $_POST['department'] : '';
    $officeLocation = isset($_POST['office-location']) ? $_POST['office-location'] : '';
    $panCard = isset($_POST['panCard']) ? $_POST['panCard'] : '';
    $govtId = isset($_POST['govtId']) ? $_POST['govtId'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $permanentAddress = isset($_POST['permanent-address']) ? $_POST['permanent-address'] : '';
    $residentialAddress = isset($_POST['residential-address']) ? $_POST['residential-address'] : '';
    $photo_path = isset($_POST['photo_path']) ? $_POST['photo_path'] : '';
    $salary= isset($_POST['salary']) ? $_POST['salary'] : '';
    $hireDate = isset($_POST['Hire-Date']) ? $_POST['Hire-Date'] : '';

    $sql = "INSERT INTO directory (`Name`, `Job Title`, `Department`, `Office_Location`, `PAN_Card_Number`, `Govt_ID_Number`, `Permanent_Address`, `Residential_Address`, `Phone`, `Email`, `Photo`, `Hire-Date`, `Salary`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing the statement: " . $con->error;
    } else {
        $stmt->bind_param("sssssssssssss", $name, $jobTitle, $department, $officeLocation, $panCard, $govtId, $permanentAddress, $residentialAddress, $phone, $email, $photo_path, $hireDate, $salary);

        if ($stmt->execute()) {
            $directory_id = $con->insert_id;

            // Insert data into the login table
            $sql = "INSERT INTO login (`Name`, `EID`, `Email`, `Password`)
                    VALUES (?, ?, ?, CONCAT(?, ?))";
            $stmt = $con->prepare($sql);
            if ($stmt !== false) {
                $stmt->bind_param("sisss", $name, $directory_id, $email, $name, $directory_id);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error preparing the statement: " . $con->error;
            }

            // Insert data into the attendence table
            $sql = "INSERT INTO attendence (`Name`, `EID`)
                    VALUES (?, ?)";
            $stmt = $con->prepare($sql);
            if ($stmt !== false) {
                $stmt->bind_param("si", $name, $directory_id);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error preparing the statement: " . $con->error;
            }

            echo "Data inserted successfully";
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
        $stmt->close();
    }
}

$con->close();
?>