<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the backend to get data
require_once 'backend.php';
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
        .system-status {
            font-size: 0.8em;
            margin-bottom: 20px;
        }
        .debug-info {
            font-size: 0.8em;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-building"></i> Anycompany Properties Sdn Bhd
            </span>
            <div>
                <a href="view_db.php" class="btn btn-sm btn-outline-light">View DB</a>
                <a href="clear_db.php" class="btn btn-sm btn-outline-light">Clear DB</a>
                <a href="debug.php" class="btn btn-sm btn-outline-light">Debug</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!$db_connected): ?>
            <div class="alert alert-warning">
                <strong>Note:</strong> Running in test mode with SQLite in-memory database. 
                To connect to a real database, update the connection parameters in backend.php.
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-plus"></i> Add New Property
                    </div>
                    <div class="card-body">
                        <form method="POST" action="backend.php">
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
                
                <!-- Direct test form for debugging -->
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-bug"></i> Test Form
                    </div>
                    <div class="card-body">
                        <form method="POST" action="direct_test.php">
                            <input type="hidden" name="action" value="test">
                            <input type="hidden" name="title" value="Quick Test Property">
                            <input type="hidden" name="property_type" value="Apartment">
                            <input type="hidden" name="price" value="300000">
                            <input type="hidden" name="size_sqft" value="800">
                            <input type="hidden" name="bedrooms" value="1">
                            <input type="hidden" name="bathrooms" value="1">
                            <input type="hidden" name="location" value="Test Location">
                            <input type="hidden" name="status" value="Available">
                            <input type="hidden" name="description" value="Quick test property description">
                            <button type="submit" class="btn btn-secondary">Run Quick Test</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <h2 class="mb-4">Property Listings</h2>
                <?php if (empty($properties)): ?>
                    <div class="alert alert-info">No properties found. Add your first property using the form.</div>
                <?php else: ?>
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
                                                <i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['bedrooms'] ?? 'N/A'); ?>
                                            </span>
                                            <span class="feature-icon">
                                                <i class="fas fa-bath"></i> <?php echo htmlspecialchars($property['bathrooms'] ?? 'N/A'); ?>
                                            </span>
                                        </div>
                                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?></p>
                                        <p class="card-text"><?php echo htmlspecialchars($property['description'] ?? ''); ?></p>
                                        
                                        <div class="d-flex justify-content-between mt-3">
                                            <form method="POST" action="backend.php" class="me-2">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="Available" <?php echo $property['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                                                    <option value="Under Contract" <?php echo $property['status'] == 'Under Contract' ? 'selected' : ''; ?>>Under Contract</option>
                                                    <option value="Sold" <?php echo $property['status'] == 'Sold' ? 'selected' : ''; ?>>Sold</option>
                                                </select>
                                            </form>
                                            <form method="POST" action="backend.php" style="display: inline;">
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
                <?php endif; ?>
            </div>
        </div>
        
        <footer class="mt-5 pt-3 border-top text-muted">
            <div class="system-status">
                <p>System Status: <?php echo $db_connected ? 'Connected to database' : 'Test mode (SQLite in-memory)'; ?></p>
            </div>
            
            <?php if ($debug_mode): ?>
            <div class="debug-info">
                <h5>Debug Information</h5>
                <p>PHP Version: <?php echo phpversion(); ?></p>
                <p>Session ID: <?php echo session_id(); ?></p>
                <p>Properties Count: <?php echo count($properties); ?></p>
                <p>POST Data: <?php echo !empty($_POST) ? 'Present' : 'Empty'; ?></p>
                <p>Session Data: <?php echo !empty($_SESSION) ? 'Present' : 'Empty'; ?></p>
            </div>
            <?php endif; ?>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
