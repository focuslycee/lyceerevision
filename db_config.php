<?php
$host = 'localhost'; // Database host
$username = 'root';  // Your MySQL username
$password = '';      // Your MySQL password
$database = 'school_db'; // Your database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

