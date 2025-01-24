<?php
require 'cdn.php'; // Include the database connection

class UserAPI {
    private $con;
    private $apiKey = "peeyush123"; // Your unique API key

    public function __construct($dbConnection) {
        $this->con = $dbConnection;
    }

    private function validateApiKey($key) {
        return $key === $this->apiKey;
    }

    private function respond($status, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function handleRequest() {
        if (!isset($_GET['apikey']) || !$this->validateApiKey($_GET['apikey'])) {
            $this->respond('error', 'Unauthorized access. Invalid API key.');
        }

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

        $stmt = $this->con->prepare("SELECT * FROM RegistarData WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $this->respond('error', 'Email already exists.');
        }

        $stmt = $this->con->prepare("INSERT INTO RegistarData (Name, email, Pass) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $password);

        if ($stmt->execute()) {
            $this->respond('success', 'User registered successfully.');
        } else {
            $this->respond('error', 'Failed to register user.');
        }
    }

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

    private function deleteUser() {
        $email = $_GET['email'] ?? null; // Use query string for DELETE request
    
        if (!$email) {
            $this->respond('error', 'Email is required.');
        }
    
        // Prepare the DELETE SQL query
        $stmt = $this->con->prepare("DELETE FROM RegistarData WHERE email = ?");
        $stmt->bind_param('s', $email);
    
        // Execute the query
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $this->respond('success', 'User deleted successfully.');
        } else {
            $this->respond('error', 'User not found or failed to delete.');
        }
    }
    
}

$api = new UserAPI($con);
$api->handleRequest();

?>
