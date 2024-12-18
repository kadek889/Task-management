<?php
// Koneksi database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'task_manager';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
