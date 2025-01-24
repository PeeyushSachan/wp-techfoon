<?php
// Database connection
$host = "m7wltxurw8d2n21q.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
$username = "bul8vkdz02hrkuai";
$password = "bvs15f11onmpqwxm";
$database = "ttw1ai74taxl76ho";
$port = "3306";

$con = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>


