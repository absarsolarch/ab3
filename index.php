<?php
// Database connection parameters
$host = "YOUR_RDS_ENDPOINT";
$dbname = "myappdb";
$user = "YOUR_DB_USER";
$password = "YOUR_DB_PASSWORD";

// Initialize message variable
$message = '';

try {
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

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
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
                    $message = "Property listed successfully!";
                    break;

                case 'update':
                    $stmt = $pdo->prepare("UPDATE properties SET status=? WHERE id=?");
                    $stmt->execute([$_POST['status'], $_POST['id']]);
                    $message = "Property status updated successfully!";
                    break;

                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM properties WHERE id=?");
                    $stmt->execute([$_POST['id']]);
                    $message = "Property listing removed successfully!";
                    break;
            }
        }
    }

    // Fetch all properties
    $stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $message = "Connection failed: " . $e->getMessage();
}

// Helper function to format price in MYR
function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anycompany Properties Sdn Bhd - Property Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
        }
        .navbar {
            background-color: var(--primary-color) !important;
        }
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .property-card {
            height: 100%;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .price-tag {
            font-size: 1.5em;
            color: var(--secondary-color);
            font-weight: bold;
        }
        .property-features {
            margin: 10px 0;
        }
        .feature-icon {
            margin-right: 15px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-building"></i> Anycompany Properties Sdn Bhd
            </span>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-plus"></i> Add New Property
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-3">
                                <label class="form-label">Property Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Property Type</label>
                                <select name="property_type" class="form-control" required>
                                    <option value="Apartment">Apartment</option>
                                    <option value="House">House</option>
                                    <option value="Land">Land</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price (MYR)</label>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Size (sq ft)</label>
                                <input type="number" name="size_sqft" class="form-control" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">Bedrooms</label>
                                    <input type="number" name="bedrooms" class="form-control">
                                </div>
                                <div class="col">
                                    <label class="form-label">Bathrooms</label>
                                    <input type="number" name="bathrooms" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Available">Available</option>
                                    <option value="Under Contract">Under Contract</option>
                                    <option value="Sold">Sold</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Property</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <h2 class="mb-4">Property Listings</h2>
                <div class="row">
                    <?php foreach ($properties as $property): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card property-card">
                                <div class="card-body">
                                    <span class="status-badge bg-<?php 
                                        echo $property['status'] == 'Available' ? 'success' : 
                                            ($property['status'] == 'Under Contract' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo htmlspecialchars($property['status']); ?>
                                    </span>
                                    <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                                    <div class="price-tag"><?php echo formatPrice($property['price']); ?></div>
                                    <div class="property-features">
                                        <span class="feature-icon">
                                            <i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['size_sqft']); ?> sq ft
                                        </span>
                                        <span class="feature-icon">
                                            <i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['bedrooms']); ?>
                                        </span>
                                        <span class="feature-icon">
                                            <i class="fas fa-bath"></i> <?php echo htmlspecialchars($property['bathrooms']); ?>
                                        </span>
                                    </div>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?></p>
                                    <p class="card-text"><?php echo htmlspecialchars($property['description']); ?></p>
                                    
                                    <div class="d-flex justify-content-between mt-3">
                                        <form method="POST" class="me-2">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Available" <?php echo $property['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                                                <option value="Under Contract" <?php echo $property['status'] == 'Under Contract' ? 'selected' : ''; ?>>Under Contract</option>
                                                <option value="Sold" <?php echo $property['status'] == 'Sold' ? 'selected' : ''; ?>>Sold</option>
                                            </select>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this property?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
