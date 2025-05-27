<?php
// Script to clear the database and start fresh

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database file path
$db_file = __DIR__ . '/properties.db';

// Check if file exists
if (file_exists($db_file)) {
    // Delete the file
    if (unlink($db_file)) {
        echo "<p>Database file deleted successfully.</p>";
    } else {
        echo "<p>Failed to delete database file.</p>";
    }
} else {
    echo "<p>Database file does not exist.</p>";
}

// Redirect to frontend
echo "<p>Redirecting to frontend in 3 seconds...</p>";
echo "<p><a href='frontend.php'>Click here if not redirected automatically</a></p>";
header("refresh:3;url=frontend.php");
?>
