<?php
require 'vendor/autoload.php'; // Path to Composer autoload file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection setup (if needed)
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "student_attendance";
$conn_attendance = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn_attendance) {
    die("Connection to database failed: " . mysqli_connect_error());
}

// Open the serial port
$port = "COM3"; // Change to your port
$baudRate = 9600;
$serial = fopen($port, "r+");

if (!$serial) {
    die("Unable to open serial port.");
}

// Create a new spreadsheet and set the header
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'UID');
$row = 2;

while (true) {
    if ($serial) {
        $data = fgets($serial);
        if ($data) {
            $uid = trim($data);
            
            // Insert UID into database (if needed)
            $sql = "INSERT INTO attendance (rfid_tag) VALUES ('$uid')";
            mysqli_query($conn_attendance, $sql);

            // Write data to Excel
            $sheet->setCellValue('A' . $row, $uid);
            $row++;
        }
    }
}

// Save the spreadsheet
$writer = new Xlsx($spreadsheet);
$writer->save('RFID_Data.xlsx');

// Close serial port
fclose($serial);

// Close database connection
mysqli_close($conn_attendance);

echo "Data has been written to RFID_Data.xlsx";
?>
