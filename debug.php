<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

echo "<h1>Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";

// Check PDO availability
echo "<h2>PDO Extensions</h2>";
echo "<p>PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "</p>";
echo "<p>PDO SQLite Available: " . (extension_loaded('pdo_sqlite') ? 'Yes' : 'No') . "</p>";
echo "<p>PDO PostgreSQL Available: " . (extension_loaded('pdo_pgsql') ? 'Yes' : 'No') . "</p>";

// Check session functionality
echo "<h2>Session Information</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . session_status() . " (1=disabled, 2=enabled but no session, 3=enabled and has session)</p>";
echo "<pre>Session Data: " . print_r($_SESSION, true) . "</pre>";

// Test SQLite in-memory database
echo "<h2>SQLite Test</h2>";
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create a test table
    $pdo->exec("CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)");
    $pdo->exec("INSERT INTO test (name) VALUES ('Test Record')");
    
    // Query the table
    $stmt = $pdo->query("SELECT * FROM test");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>SQLite Test: Success</p>";
    echo "<pre>Test Data: " . print_r($result, true) . "</pre>";
} catch (Exception $e) {
    echo "<p>SQLite Test: Failed</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test form submission
echo "<h2>Form Submission Test</h2>";
echo "<form method='POST' action='debug.php'>";
echo "<input type='hidden' name='action' value='test'>";
echo "<input type='hidden' name='title' value='Test Property'>";
echo "<input type='hidden' name='property_type' value='Apartment'>";
echo "<input type='hidden' name='price' value='500000'>";
echo "<input type='hidden' name='size_sqft' value='1000'>";
echo "<input type='hidden' name='bedrooms' value='2'>";
echo "<input type='hidden' name='bathrooms' value='2'>";
echo "<input type='hidden' name='location' value='Test Location'>";
echo "<input type='hidden' name='status' value='Available'>";
echo "<input type='hidden' name='description' value='Test Description'>";
echo "<button type='submit'>Test Form Submission</button>";
echo "</form>";

// Process test form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'test') {
    echo "<h3>Form Data Received:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    try {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create properties table
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
        
        // Insert test property
        $stmt = $pdo->prepare("INSERT INTO properties (title, property_type, price, size_sqft, bedrooms, bathrooms, location, status, description) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['property_type'],
            $_POST['price'],
            $_POST['size_sqft'],
            $_POST['bedrooms'],
            $_POST['bathrooms'],
            $_POST['location'],
            $_POST['status'],
            $_POST['description']
        ]);
        
        $lastId = $pdo->lastInsertId();
        echo "<p>Test Insert: Success (ID: $lastId)</p>";
        
        // Verify the insert
        $stmt = $pdo->query("SELECT * FROM properties");
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>Properties Data: " . print_r($properties, true) . "</pre>";
    } catch (Exception $e) {
        echo "<p>Test Insert: Failed</p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$files = ['index.php', 'frontend.php', 'backend.php', 'test.php'];
echo "<ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li>$file: " . substr(sprintf('%o', fileperms($file)), -4) . " (exists)</li>";
    } else {
        echo "<li>$file: does not exist</li>";
    }
}
echo "</ul>";

// Check for common issues
echo "<h2>Common Issues Check</h2>";
echo "<ul>";
echo "<li>POST data processing: " . (empty($_POST) ? "No POST data received" : "POST data available") . "</li>";
echo "<li>Form action URLs: Check that all forms point to the correct URL</li>";
echo "<li>Database connection: Make sure the database connection parameters are correct</li>";
echo "<li>File permissions: Ensure all files have proper read permissions</li>";
echo "<li>Error logging: Check PHP error logs for additional information</li>";
echo "</ul>";
?>
