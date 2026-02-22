<?php
// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "notices_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create a notice
function createNotice($title, $content) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notices (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();
}

// Function to fetch all notices
function getNotices() {
    global $conn;
    $result = $conn->query("SELECT * FROM notices");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to handle API endpoints
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    createNotice($data['title'], $data['content']);
    echo json_encode(['status' => 'Notice created successfully.']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $notices = getNotices();
    echo json_encode($notices);
}
?>