<?php
$host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'rmo';

$conn = mysqli_connect($host, $db_username, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
