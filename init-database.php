<?php
require_once 'config/database.php';

try {
    // Initialize database
    initializeDatabase();
    
    $pdo = getDBConnection();
    
    // Add sample companies
    $companies = [
        ['TechCorp', 'Leading technology company specializing in software development', 'New York, NY', 'techcorp@example.com'],
        ['MarketingPro', 'Digital marketing agency helping businesses grow online', 'Los Angeles, CA', 'info@marketingpro.com'],
        ['DesignStudio', 'Creative design studio for web and mobile applications', 'San Francisco, CA', 'hello@designstudio.com'],
        ['SalesForce', 'Sales and business development solutions', 'Chicago, IL', 'contact@salesforce.com'],
        ['FinanceHub', 'Financial services and consulting firm', 'Boston, MA', 'info@financehub.com'],
        ['HealthCare Plus', 'Healthcare technology and services', 'Austin, TX', 'care@healthcareplus.com']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO companies (name, description, location, contact_email) VALUES (?, ?, ?, ?)");
    foreach ($companies as $company) {
        $stmt->execute($company);
    }
    
    // Add sample jobs
    $jobs = [
        ['Software Developer', 'We are looking for a skilled software developer to join our team. Experience with PHP, JavaScript, and MySQL required.', 'PHP, JavaScript, MySQL, Git', 1, 1, 'New York, NY', 'full-time', 70000, 90000, 'mid'],
        ['Digital Marketing Specialist', 'Join our marketing team to help clients grow their online presence. Experience with SEO, SEM, and social media required.', 'SEO, SEM, Social Media, Analytics', 2, 1, 'Los Angeles, CA', 'full-time', 50000, 70000, 'mid'],
        ['UI/UX Designer', 'Create beautiful and functional user interfaces for web and mobile applications. Experience with Figma, Adobe Creative Suite required.', 'Figma, Adobe Creative Suite, Prototyping', 3, 1, 'San Francisco, CA', 'full-time', 60000, 80000, 'mid'],
        ['Sales Representative', 'Help us grow our client base through effective sales strategies. Strong communication skills required.', 'Sales, Communication, CRM', 4, 1, 'Chicago, IL', 'full-time', 40000, 60000, 'entry'],
        ['Financial Analyst', 'Analyze financial data and provide insights to help business decisions. Experience with Excel and financial modeling required.', 'Excel, Financial Modeling, Analysis', 5, 1, 'Boston, MA', 'full-time', 55000, 75000, 'mid'],
        ['Healthcare Administrator', 'Manage healthcare operations and ensure quality patient care. Experience in healthcare administration required.', 'Healthcare, Administration, Management', 6, 1, 'Austin, TX', 'full-time', 45000, 65000, 'mid'],
        ['Frontend Developer', 'Build responsive and interactive web applications using modern technologies. Experience with React, Vue.js required.', 'React, Vue.js, HTML, CSS, JavaScript', 1, 1, 'Remote', 'full-time', 65000, 85000, 'mid'],
        ['Content Writer', 'Create engaging content for our marketing campaigns. Strong writing skills and SEO knowledge required.', 'Content Writing, SEO, Copywriting', 2, 1, 'Los Angeles, CA', 'part-time', 30000, 45000, 'entry'],
        ['Product Manager', 'Lead product development from concept to launch. Experience with agile methodologies required.', 'Product Management, Agile, User Research', 3, 1, 'San Francisco, CA', 'full-time', 80000, 120000, 'senior'],
        ['Data Analyst', 'Analyze data to provide insights and support business decisions. Experience with SQL and data visualization required.', 'SQL, Data Visualization, Python', 5, 1, 'Boston, MA', 'full-time', 60000, 80000, 'mid']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO jobs (title, description, requirements, company_id, employer_id, location, type, salary_min, salary_max, experience_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($jobs as $job) {
        $stmt->execute($job);
    }
    
    // Insert default admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@gmail.com', $adminPassword, 'Administrator', 'admin']);
    
    echo "<h2>Database initialized successfully!</h2>";
    echo "<p>Sample companies and jobs have been added to the database.</p>";
    echo "<p><a href='index.php'>Go to JobHub</a></p>";
    
} catch (Exception $e) {
    echo "<h2>Error initializing database:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?> 