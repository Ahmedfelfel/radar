<?php
// Database credentials
$server = "192.168.1.6:8080";
$username = "AhmedHassan";
$password = "052577";
$dbname = "traffic police";

// Create connection
$conn = new mysqli($server, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the car registration number from the ESP8266 POST request
$carReg = $_POST['carReg'];

// Initialize the stolen variable
$stolen = 0;

// Check if the carReg is in the stolen_cars table
$sql = "SELECT * FROM stolen_cars WHERE Car_Reg = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carReg);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Car is stolen
    $stolen = 1;
    // Move the record to stolen_cars_captured
    $row = $result->fetch_assoc();
    $insert_sql = "INSERT INTO stolen_cars_captured (Car_Id, Car_Reg) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("is", $row['Car_Id'], $row['Car_Reg']);
    $insert_stmt->execute();
    $insert_stmt->close();
    // Delete the record from stolen_cars
   // $delete_sql = "DELETE FROM stolen_cars WHERE Car_Reg = ?";
    //$delete_stmt = $conn->prepare($delete_sql);
    //$delete_stmt->bind_param("s", $carReg);
    //$delete_stmt->execute();
    //$delete_stmt->close();
}

// Send the stolen status back to the ESP8266
echo "stolen=" . $stolen;

$stmt->close();
$conn->close();
?>
