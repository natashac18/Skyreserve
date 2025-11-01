<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "skyreserve";

$conn = new mysqli($host, $user, $password, $database);


if ($conn->connect_error){
    echo "Connection failed";
    // die("Database Connection Failed: ". $conn->connect_error);
}

?>