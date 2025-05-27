<?php
// Start session for message passing between pages
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h2>Form Data Received:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Store in session for testing
    $_SESSION['test_form_data'] = $_POST;
    
    echo "<p>Form data has been stored in session.</p>";
    echo "<p><a href='test_form.php'>Back to form</a></p>";
    exit;
}

// Check if we have test data in session
$has_test_data = isset($_SESSION['test_form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Form Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Property Form Test</h1>
        
        <div class="alert alert-info">
            <p>This is a simple test form to verify that form submissions are working correctly.</p>
            <p>The form will submit to this same page and display the received data.</p>
        </div>
        
        <?php if ($has_test_data): ?>
        <div class="alert alert-success">
            <h4>Previous Form Submission:</h4>
            <pre><?php print_r($_SESSION['test_form_data']); ?></pre>
            <p><a href="?clear=1" class="btn btn-sm btn-warning">Clear Data</a></p>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                Test Property Form
            </div>
            <div class="card-body">
                <form method="POST" action="test_form.php">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Property Title</label>
                        <input type="text" name="title" class="form-control" value="Test Property" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Property Type</label>
                        <select name="property_type" class="form-control" required>
                            <option value="Apartment" selected>Apartment</option>
                            <option value="House">House</option>
                            <option value="Land">Land</option>
                            <option value="Commercial">Commercial</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (MYR)</label>
                        <input type="number" name="price" class="form-control" value="500000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Size (sq ft)</label>
                        <input type="number" name="size_sqft" class="form-control" value="1000" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Bedrooms</label>
                            <input type="number" name="bedrooms" class="form-control" value="2">
                        </div>
                        <div class="col">
                            <label class="form-label">Bathrooms</label>
                            <input type="number" name="bathrooms" class="form-control" value="2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="Test Location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Available" selected>Available</option>
                            <option value="Under Contract">Under Contract</option>
                            <option value="Sold">Sold</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">This is a test property description.</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Test Form</button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="frontend.php" class="btn btn-secondary">Go to Main Application</a>
        </div>
    </div>
</body>
</html>
<?php
// Clear test data if requested
if (isset($_GET['clear'])) {
    unset($_SESSION['test_form_data']);
    header("Location: test_form.php");
    exit;
}
?>
