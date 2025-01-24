<?php
// Include the database connection
require 'cdn.php';

// Define the API class
class UserAPI {
    private $con;
    private $apiKey = "peeyush";

    // Constructor to initialize the database connection
    public function __construct($dbConnection) {
        $this->con = $dbConnection;
    }

    // Validate the API key
    private function validateApiKey($key) {
        return $key === $this->apiKey;
    }

    // Respond to API requests
    private function respond($status, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Handle requests
    public function handleRequest() {
        // Validate API key
        if (!isset($_GET['apikey']) || !$this->validateApiKey($_GET['apikey'])) {
            $this->respond('error', 'Unauthorized access. Invalid API key.');
        }

        // Route based on HTTP method
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'POST':
                $this->insertUser();
                break;
            case 'GET':
                $this->getUser();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
            default:
                $this->respond('error', 'Invalid request method.');
        }
    }

    // Insert a user
    private function insertUser() {
        $name = $_POST['Name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['Pass'] ?? null;
        $cpass = $_POST['cpass'] ?? null;

        if (!$name || !$email || !$password || !$cpass) {
            $this->respond('error', 'All fields are required.');
        }

        if ($password !== $cpass) {
            $this->respond('error', 'Passwords do not match.');
        }

        // Check if email already exists
        $stmt = $this->con->prepare("SELECT * FROM RegistarData WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $this->respond('error', 'Email already exists.');
        }

        // Insert the user
        $stmt = $this->con->prepare("INSERT INTO RegistarData (Name, email, Pass) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $password);
        if ($stmt->execute()) {
            $this->respond('success', 'User registered successfully.');
        } else {
            $this->respond('error', 'Failed to register user.');
        }
    }

    // Retrieve a user by email
    private function getUser() {
        $email = $_GET['email'] ?? null;

        if (!$email) {
            $this->respond('error', 'Email is required.');
        }

        $stmt = $this->con->prepare("SELECT Name, email FROM RegistarData WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->respond('success', 'User found.', $result->fetch_assoc());
        } else {
            $this->respond('error', 'User not found.');
        }
    }

    // Delete a user by email
    private function deleteUser() {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $email = $_DELETE['email'] ?? null;

        if (!$email) {
            $this->respond('error', 'Email is required.');
        }

        $stmt = $this->con->prepare("DELETE FROM RegistarData WHERE email = ?");
        $stmt->bind_param('s', $email);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $this->respond('success', 'User deleted successfully.');
        } else {
            $this->respond('error', 'User not found or failed to delete.');
        }
    }
}

// Create an instance of the UserAPI class and handle the request
$api = new UserAPI($con);
$api->handleRequest();
?>
