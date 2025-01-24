<?php
$host = 'm7wltxurw8d2n21q.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
$username = 'bul8vkdz02hrkuai';
$password = 'bvs15f11onmpqwxm';
$database = 'ttw1ai74taxl76ho';

// Create a new connection to the MySQL database
$con = new mysqli($host, $username, $password, $database, 3306);

// Check if the connection is successful
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}
?>
