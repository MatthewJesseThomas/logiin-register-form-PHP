<?php
session_start();
class Database
{
private $host;
private $user;
private $password;
private $db;
private $port;
public $conn;

public function __construct()
{
$envFilePath = __DIR__ . '/.env';
if (file_exists($envFilePath)) {
$envVariables = parse_ini_file($envFilePath);
foreach ($envVariables as $key => $value) {
$_ENV[$key] = $value;
}
}

$this->host = $_ENV['DB_HOST'] ?? 'localhost';
$this->user = $_ENV['DB_USER'] ?? 'root';
$this->password = $_ENV['DB_PASSWORD'] ?? '';
$this->db = $_ENV['DB_NAME'] ?? 'mydatabase';
$this->port = $_ENV['DB_PORT'] ?? '3306';

$this->conn = new mysqli($this->host, $this->user, $this->password, $this->db, $this->port);
if ($this->conn->connect_errno) {
echo "Failed to connect to MySQL: " . $this->conn->connect_error;
exit();
}
}

// Other database-related methods
}

$database = new Database();
$conn = $database->conn;