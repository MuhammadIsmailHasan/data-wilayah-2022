<?php 

$servername = "localhost";
$username = "root";
$password = "";
$database = "wilayah_2022";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error) . PHP_EOL;
}
echo "Connected successfully" . PHP_EOL;
?>