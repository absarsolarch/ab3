<?php
// Script to view the raw database contents

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database file path
$db_file = __DIR__ . '/properties.db';

echo "<h1>Database Contents</h1>";

// Check if file exists
if (file_exists($db_file)) {
    echo "<p>Database file exists: " . $db_file . "</p>";
    echo "<p>File size: " . filesize($db_file) . " bytes</p>";
    
    try {
        // Connect to the database
        $pdo = new PDO('sqlite:' . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get table structure
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        echo "<h2>Tables:</h2>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table['name'] . "</li>";
        }
        echo "</ul>";
        
        // Get properties data
        $stmt = $pdo->query("SELECT * FROM properties");
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Properties (" . count($properties) . "):</h2>";
        
        if (count($properties) > 0) {
            echo "<table border='1' cellpadding='5'>";
            
            // Table header
            echo "<tr>";
            foreach (array_keys($properties[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Table data
            foreach ($properties as $property) {
                echo "<tr>";
                foreach ($property as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No properties found in database.</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Database file does not exist.</p>";
}

echo "<p><a href='frontend.php'>Back to Frontend</a> | <a href='clear_db.php'>Clear Database</a></p>";
?>
