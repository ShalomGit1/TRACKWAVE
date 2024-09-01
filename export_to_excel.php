<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Sample data
$data = [
    ['ID', 'RFID Tag', 'Timestamp'],
    [1, '1234567890', '2024-07-26 14:30:00'],
    [2, '0987654321', '2024-07-26 14:31:00'],
    // Add more rows as needed
];

// Write data to the spreadsheet
$sheet->fromArray($data, null, 'A1');

// Create a writer instance
$writer = new Xlsx($spreadsheet);

// Set the filename and save the file
$filename = 'example.xlsx';
$writer->save($filename);

echo "Excel file has been created: " . $filename;
