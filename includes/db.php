<?php
// اتصال به پایگاه داده
$host = 'localhost';
$dbname = 'nima_gamenet';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("خطا در اتصال به پایگاه داده: " . $conn->connect_error);
}

// تنظیم زبان پایگاه داده به فارسی
$conn->query("SET NAMES 'utf8'");
$conn->query("SET CHARACTER SET utf8");
$conn->query("SET character_set_connection=utf8");
?>