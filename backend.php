<?php
// Start session for message passing between pages
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = "YOUR_RDS_ENDPOINT";
$dbname = "myappdb";
$user = "YOUR_DB_USER";
$password = "YOUR_DB_PASSWORD";

// Initialize variables
$message = '';
$properties = [];
$db_connected = false;

// Debug mode - set to true to see detailed errors
$debug_mode = true;

/**
 * Connect to the database
 * @return PDO|null Database connection or null on failure
 */
function connectToDatabase() {
    global $host, $dbname, $user, $password, $debug_mode;
    
    try {
        // For testing without a real database, use SQLite file-based database
        if ($host === "YOUR_RDS_ENDPOINT" || $host === "TEST_MODE") {
            // Use a file-based SQLite database instead of in-memory
            $db_file = __DIR__ . '/properties.db';
            $pdo = new PDO('sqlite:' . $db_file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create table for SQLite (different syntax)
            $pdo->exec("CREATE TABLE IF NOT EXISTS properties (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                property_type TEXT NOT NULL,
                price NUMERIC(12,2) NOT NULL,
                size_sqft INTEGER NOT NULL,
                bedrooms INTEGER,
                bathrooms INTEGER,
                location TEXT NOT NULL,
                status TEXT DEFAULT 'Available',
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Add some test data if table is empty
            $count = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
            if ($count == 0) {
                $stmt = $pdo->prepare("INSERT INTO properties (title, property_type, price, size_sqft, bedrooms, bathrooms, location, status, description) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    'Test Property 1',
                    'Apartment',
                    450000,
                    1200,
                    3,
                    2,
                    'Kuala Lumpur',
                    'Available',
                    'This is a test property for development purposes.'
                ]);
            }
        } else {
            // Real PostgreSQL connection
            $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create table if not exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS properties (
                id SERIAL PRIMARY KEY,
                title VARCHAR(200) NOT NULL,
                property_type VARCHAR(50) NOT NULL,
                price DECIMAL(12,2) NOT NULL,
                size_sqft INTEGER NOT NULL,
                bedrooms INTEGER,
                bathrooms INTEGER,
                location VARCHAR(200) NOT NULL,
                status VARCHAR(50) DEFAULT 'Available',
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        return $pdo;
    } catch(PDOException $e) {
        if ($debug_mode) {
            echo "<!-- Database connection error: " . $e->getMessage() . " -->";
        }
        return null;
    }
}

// Try to connect to the database
$pdo = connectToDatabase();
$db_connected = ($pdo instanceof PDO);

// Process form submissions if database is connected
if ($db_connected && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    // Debug output
                    if ($debug_mode) {
                        file_put_contents('debug_log.txt', "Form data received: " . print_r($_POST, true), FILE_APPEND);
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO properties (title, property_type, price, size_sqft, bedrooms, bathrooms, location, status, description) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['property_type'],
                        $_POST['price'],
                        $_POST['size_sqft'],
                        $_POST['bedrooms'] ?? null,
                        $_POST['bathrooms'] ?? null,
                        $_POST['location'],
                        $_POST['status'],
                        $_POST['description'] ?? ''
                    ]);
                    $_SESSION['message'] = "Property listed successfully!";
                    break;

                case 'update':
                    $stmt = $pdo->prepare("UPDATE properties SET status=? WHERE id=?");
                    $stmt->execute([$_POST['status'], $_POST['id']]);
                    $_SESSION['message'] = "Property status updated successfully!";
                    break;

                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM properties WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $_SESSION['message'] = "Property listing removed successfully!";
                    break;
                    
                case 'test':
                    // Special case for testing
                    $_SESSION['message'] = "Test successful!";
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = $debug_mode ? $e->getMessage() : "An error occurred while processing your request.";
            if ($debug_mode) {
                file_put_contents('debug_log.txt', "Database error: " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }
        
        // Redirect back to frontend after processing
        if (!isset($_POST['no_redirect'])) {
            header("Location: frontend.php");
            exit;
        }
    }
}

// Check for session messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Fetch all properties if database is connected
if ($db_connected) {
    try {
        $stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC");
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($debug_mode) {
            file_put_contents('debug_log.txt', "Properties fetched: " . count($properties) . "\n", FILE_APPEND);
        }
    } catch (PDOException $e) {
        if ($debug_mode) {
            $error = $e->getMessage();
            file_put_contents('debug_log.txt', "Error fetching properties: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}

// Helper function to format price in MYR
function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
}

// If this file is accessed directly (API mode), return JSON
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    
    // Check if this is a test request
    if (isset($_GET['test'])) {
        echo json_encode([
            'status' => 'ok',
            'db_connected' => $db_connected,
            'message' => 'Backend is functioning correctly',
            'post_data' => $_POST,
            'session_data' => $_SESSION
        ]);
        exit;
    }
    
    // Simple API endpoint handling
    if (isset($_GET['api'])) {
        if (!$db_connected) {
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
        
        try {
            switch ($_GET['api']) {
                case 'properties':
                    echo json_encode($properties);
                    break;
                case 'property':
                    if (isset($_GET['id'])) {
                        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
                        $stmt->execute([$_GET['id']]);
                        $property = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo json_encode($property ?: ['error' => 'Property not found']);
                    } else {
                        echo json_encode(['error' => 'Property ID required']);
                    }
                    break;
                default:
                    echo json_encode(['error' => 'Unknown API endpoint']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $debug_mode ? $e->getMessage() : 'API error']);
        }
        exit;
    }
}
?>
