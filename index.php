<?php
include('cdn.php');

// Simple API example
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(["message" => "Welcome to the API!"]);
}
?>
