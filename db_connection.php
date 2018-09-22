<?php
// Create connection to the database 
global $DB_HOST; 
global $DB_USER; 
global $DB_PASSWORD; 
global $DB_NAME; 

$DB_HOST = 'localhost';
$DB_USER = 'admin_myscriptz';
$DB_PASSWORD = 'AXKGEmsATs3Tp2F';
$DB_NAME = 'myscriptz';

//connection string
global $conn;
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);

// Test if connection succeeded
if ($conn->connect_error){/*report error */die("Database connection failed: ".$conn->connect_error);}
?>
