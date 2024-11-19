<?php
$host = 'localhost';       // Host name
$user = 'root';            // MySQL username
$password = '';            // MySQL password (leave empty for default)
$dbname = 'rental';        // Database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
