<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add some data
$sheet->setCellValue('A1', 'Hello');
$sheet->setCellValue('B1', 'World');

// Write an .xlsx file
$writer = new Xlsx($spreadsheet);
$filePath = 'example.xlsx';
$writer->save($filePath);

echo "Excel file created: $filePath";
?>
