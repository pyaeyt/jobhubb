<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>JobHub Current Status Test</h2>";

// Test 1: Check if files exist
echo "<h3>1. File Check:</h3>";
$files = ['config/database.php', 'includes/functions.php', 'theme/templates.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 2: Check database connection
echo "<h3>2. Database Connection Test:</h3>";
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobs");
    $result = $stmt->fetch();
    echo "✅ Database query successful. Jobs count: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 3: Check functions
echo "<h3>3. Functions Test:</h3>";
try {
    require_once 'includes/functions.php';
    echo "✅ Functions file included successfully<br>";
    echo "✅ isLoggedIn function available: " . (function_exists('isLoggedIn') ? 'Yes' : 'No') . "<br>";
    echo "✅ getAllJobs function available: " . (function_exists('getAllJobs') ? 'Yes' : 'No') . "<br>";
} catch (Exception $e) {
    echo "❌ Functions error: " . $e->getMessage() . "<br>";
}

// Test 4: Check template system
echo "<h3>4. Template System Test:</h3>";
if (file_exists('theme/templates.php')) {
    echo "✅ Template file exists<br>";
} else {
    echo "❌ Template file missing<br>";
}

// Test 5: Check sample data
echo "<h3>5. Sample Data Test:</h3>";
try {
    $pdo = getDBConnection();
    
    // Check users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $users = $stmt->fetch()['count'];
    echo "Users: $users<br>";
    
    // Check jobs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobs");
    $jobs = $stmt->fetch()['count'];
    echo "Jobs: $jobs<br>";
    
    // Check companies
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM companies");
    $companies = $stmt->fetch()['count'];
    echo "Companies: $companies<br>";
    
} catch (Exception $e) {
    echo "❌ Data check error: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Next Steps:</h3>";
echo "<p>If all tests pass above, your application should be working correctly.</p>";
echo "<p><a href='index.php'>Go to JobHub Homepage</a></p>";
echo "<p><a href='admin/'>Go to Admin Dashboard</a></p>";
?> 