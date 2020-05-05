<?php

//define mysql configuration here
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "bs23_task";

$conn = new \mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}