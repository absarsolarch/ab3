<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

echo "<h1>Direct Database Test</h1>";

// Test SQLite in-memory database
echo "<h2>SQLite Test</h2>";
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table for SQLite
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
    
    echo "<p>Table created successfully</p>";
    
    // Insert test property
    $stmt = $pdo->prepare("INSERT INTO properties (title, property_type, price, size_sqft, bedrooms, bathrooms, location, status, description) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Direct Test Property',
        'Apartment',
        450000,
        1200,
        3,
        2,
        'Kuala Lumpur',
        'Available',
        'This is a direct test property.'
    ]);
    
    $lastId = $pdo->lastInsertId();
    echo "<p>Test property inserted successfully with ID: $lastId</p>";
    
    // Verify the insert
    $stmt = $pdo->query("SELECT * FROM properties");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Properties in database:</h3>";
    echo "<pre>";
    print_r($properties);
    echo "</pre>";
    
    echo "<p>SQLite Test: Success</p>";
} catch (Exception $e) {
    echo "<p>SQLite Test: Failed</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test with our backend code
echo "<h2>Backend Integration Test</h2>";
try {
    // Include the backend file
    require_once 'backend.php';
    
    echo "<p>Backend included successfully</p>";
    echo "<p>Database connected: " . ($db_connected ? 'Yes' : 'No') . "</p>";
    
    if ($db_connected) {
        echo "<h3>Properties from backend:</h3>";
        echo "<pre>";
        print_r($properties);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p>Backend Test: Failed</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Test direct form submission
echo "<h2>Direct Form Submission Test</h2>";
echo "<form method='POST' action='backend.php'>";
echo "<input type='hidden' name='action' value='create'>";
echo "<input type='hidden' name='title' value='Direct Form Test'>";
echo "<input type='hidden' name='property_type' value='House'>";
echo "<input type='hidden' name='price' value='750000'>";
echo "<input type='hidden' name='size_sqft' value='2000'>";
echo "<input type='hidden' name='bedrooms' value='4'>";
echo "<input type='hidden' name='bathrooms' value='3'>";
echo "<input type='hidden' name='location' value='Direct Test Location'>";
echo "<input type='hidden' name='status' value='Available'>";
echo "<input type='hidden' name='description' value='This is a direct form test.'>";
echo "<input type='hidden' name='no_redirect' value='1'>";
echo "<button type='submit'>Submit Direct Form Test</button>";
echo "</form>";

// Show links to other pages
echo "<h2>Navigation</h2>";
echo "<ul>";
echo "<li><a href='frontend.php'>Go to Frontend</a></li>";
echo "<li><a href='backend.php?test=1'>Test Backend API</a></li>";
echo "<li><a href='debug.php'>Run Debug Script</a></li>";
echo "<li><a href='test_form.php'>Test Form Submission</a></li>";
echo "</ul>";
?>
