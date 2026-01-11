<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>JobHub Database Setup</h2>";

try {
    require_once 'config/database.php';
    
    // Connect to MySQL without specifying database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "✅ Database 'jobhub' created/verified<br>";
    
    // Use the database
    $pdo->exec("USE " . DB_NAME);
    
    // Create tables
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('user', 'employer', 'admin') DEFAULT 'user',
            phone VARCHAR(20),
            address TEXT,
            bio TEXT,
            profile_image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "companies" => "CREATE TABLE IF NOT EXISTS companies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            logo VARCHAR(255),
            website VARCHAR(255),
            location VARCHAR(100),
            industry VARCHAR(100),
            size VARCHAR(50),
            founded_year INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "jobs" => "CREATE TABLE IF NOT EXISTS jobs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            requirements TEXT,
            company_id INT,
            employer_id INT,
            location VARCHAR(100),
            type ENUM('full-time', 'part-time', 'contract', 'internship') DEFAULT 'full-time',
            salary_min DECIMAL(10,2),
            salary_max DECIMAL(10,2),
            experience_level ENUM('entry', 'mid', 'senior', 'executive') DEFAULT 'mid',
            status ENUM('active', 'inactive', 'closed') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "applications" => "CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            job_id INT NOT NULL,
            user_id INT NOT NULL,
            cover_letter TEXT,
            resume_path VARCHAR(255),
            status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_application (job_id, user_id)
        )",
        
        "skills" => "CREATE TABLE IF NOT EXISTS skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL
        )"
    ];
    
    foreach ($tables as $tableName => $sql) {
        $pdo->exec($sql);
        echo "✅ Table '$tableName' created/verified<br>";
    }
    
    // Insert default admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@jobhub.com', $adminPassword, 'Administrator', 'admin']);
    echo "✅ Admin user created<br>";
    
    // Insert sample skills
    $skills = ['PHP', 'JavaScript', 'Python', 'Java', 'React', 'Vue.js', 'MySQL', 'MongoDB', 'AWS', 'Docker', 'Git', 'HTML/CSS'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO skills (name) VALUES (?)");
    foreach ($skills as $skill) {
        $stmt->execute([$skill]);
    }
    echo "✅ Sample skills added<br>";
    
    // Insert sample companies
    $companies = [
        ['TechCorp', 'Leading technology company', 'San Francisco, CA', 'Technology', '500-1000', 2010],
        ['InnovateSoft', 'Innovative software solutions', 'New York, NY', 'Technology', '100-500', 2015],
        ['DataFlow Inc', 'Data analytics solutions', 'Austin, TX', 'Technology', '50-100', 2018]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO companies (name, description, location, industry, size, founded_year) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($companies as $company) {
        $stmt->execute($company);
    }
    echo "✅ Sample companies added<br>";
    
    // Insert sample jobs
    $jobs = [
        ['Senior PHP Developer', 'We are looking for an experienced PHP developer.', 'Strong PHP skills, Laravel experience', 1, 'San Francisco, CA', 'full-time', 80000, 120000, 'senior'],
        ['Frontend Developer', 'Join our frontend team to build beautiful interfaces.', 'JavaScript, React/Vue.js, HTML/CSS', 2, 'New York, NY', 'full-time', 70000, 100000, 'mid'],
        ['Data Analyst', 'Help us analyze data and provide insights.', 'Python, SQL, Excel', 3, 'Austin, TX', 'full-time', 60000, 85000, 'entry']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO jobs (title, description, requirements, company_id, location, type, salary_min, salary_max, experience_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($jobs as $job) {
        $stmt->execute($job);
    }
    echo "✅ Sample jobs added<br>";
    
    echo "<h3>🎉 Setup Complete!</h3>";
    echo "<p>Your JobHub application is now ready to use.</p>";
    echo "<p><strong>Admin Login:</strong><br>";
    echo "Email: admin@jobhub.com<br>";
    echo "Password: admin123</p>";
    echo "<p><a href='index.php'>Go to JobHub Homepage</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?> 