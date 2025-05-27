<?php
/**
 * Test script for the property management application
 * This script tests the basic functionality of the backend and frontend
 */

// Set test mode
$_ENV['TEST_MODE'] = true;

// Function to run tests
function runTests() {
    $results = [];
    
    // Test 1: Check if backend.php exists
    $results['backend_exists'] = file_exists('backend.php');
    
    // Test 2: Check if frontend.php exists
    $results['frontend_exists'] = file_exists('frontend.php');
    
    // Test 3: Test backend API
    $api_response = file_get_contents('http://localhost/backend.php?test=1');
    $api_data = json_decode($api_response, true);
    $results['backend_api'] = isset($api_data['status']) && $api_data['status'] === 'ok';
    
    // Test 4: Test database connection (should work in test mode with SQLite)
    $results['database_connection'] = isset($api_data['db_connected']) && $api_data['db_connected'] === true;
    
    // Test 5: Test property creation
    $ch = curl_init('http://localhost/backend.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'action' => 'test',
        'no_redirect' => '1'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $results['form_submission'] = $response !== false;
    
    // Test 6: Test property listing API
    $properties_response = file_get_contents('http://localhost/backend.php?api=properties');
    $properties_data = json_decode($properties_response, true);
    $results['property_listing'] = is_array($properties_data);
    
    return $results;
}

// Check if this is a CLI request
if (php_sapi_name() === 'cli') {
    echo "Running tests...\n";
    $results = runTests();
    
    echo "\nTest Results:\n";
    foreach ($results as $test => $passed) {
        echo str_pad($test, 25) . ": " . ($passed ? "PASSED" : "FAILED") . "\n";
    }
    
    $passed = array_filter($results);
    $total = count($results);
    echo "\nSummary: " . count($passed) . "/" . $total . " tests passed.\n";
    
    exit(count($passed) === $total ? 0 : 1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management System - Tests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Property Management System Tests</h1>
        
        <div class="alert alert-info">
            <p>This page runs tests on the property management system to verify it's working correctly.</p>
            <p>Note: For these tests to work, you need to be running a local PHP server.</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                Test Results
            </div>
            <div class="card-body">
                <div id="test-results">Running tests...</div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="frontend.php" class="btn btn-primary">Go to Application</a>
        </div>
    </div>
    
    <script>
        // Simple function to simulate running tests in browser
        function runBrowserTests() {
            const tests = [
                { name: 'Backend file exists', test: async () => {
                    try {
                        const response = await fetch('backend.php');
                        return response.ok;
                    } catch (e) {
                        return false;
                    }
                }},
                { name: 'Frontend file exists', test: async () => {
                    try {
                        const response = await fetch('frontend.php');
                        return response.ok;
                    } catch (e) {
                        return false;
                    }
                }},
                { name: 'Backend API responds', test: async () => {
                    try {
                        const response = await fetch('backend.php?test=1');
                        const data = await response.json();
                        return data.status === 'ok';
                    } catch (e) {
                        return false;
                    }
                }},
                { name: 'Database connection works', test: async () => {
                    try {
                        const response = await fetch('backend.php?test=1');
                        const data = await response.json();
                        return data.db_connected === true;
                    } catch (e) {
                        return false;
                    }
                }},
                { name: 'Property API endpoint works', test: async () => {
                    try {
                        const response = await fetch('backend.php?api=properties');
                        const data = await response.json();
                        return Array.isArray(data);
                    } catch (e) {
                        return false;
                    }
                }}
            ];
            
            return Promise.all(tests.map(async (test) => {
                const result = await test.test();
                return { name: test.name, passed: result };
            }));
        }
        
        // Run tests and display results
        async function displayTestResults() {
            const resultsDiv = document.getElementById('test-results');
            resultsDiv.innerHTML = '<p>Running tests...</p>';
            
            try {
                const results = await runBrowserTests();
                
                let html = '<table class="table">';
                html += '<thead><tr><th>Test</th><th>Result</th></tr></thead>';
                html += '<tbody>';
                
                let passCount = 0;
                results.forEach(result => {
                    if (result.passed) passCount++;
                    html += `<tr>
                        <td>${result.name}</td>
                        <td><span class="badge bg-${result.passed ? 'success' : 'danger'}">${result.passed ? 'PASSED' : 'FAILED'}</span></td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                html += `<p>Summary: ${passCount}/${results.length} tests passed.</p>`;
                
                if (passCount === results.length) {
                    html += '<div class="alert alert-success">All tests passed! The application should be working correctly.</div>';
                } else {
                    html += '<div class="alert alert-warning">Some tests failed. The application may not work correctly.</div>';
                }
                
                resultsDiv.innerHTML = html;
            } catch (error) {
                resultsDiv.innerHTML = `<div class="alert alert-danger">Error running tests: ${error.message}</div>`;
            }
        }
        
        // Run tests when page loads
        window.addEventListener('load', displayTestResults);
    </script>
</body>
</html>
