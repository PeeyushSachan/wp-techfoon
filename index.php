<?php
include('cdn.php');

// Simple API exampleasdfasfasdfasdfasfsafasf
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(["message" => "Welcome to the API!"]);
}
?>


