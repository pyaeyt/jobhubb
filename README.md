# JobHub - Job Portal Application

A comprehensive job portal web application built with PHP, MySQL, and modern web technologies. JobHub connects job seekers with employers, providing a complete platform for job postings, applications, and management.

## 🚀 Features

### For Job Seekers
- **User Registration & Authentication** - Secure registration and login system
- **Job Browsing** - Search and filter jobs by location, type, experience level, and keywords
- **Job Applications** - Apply to jobs with resume upload and cover letter
- **Application Tracking** - View and track application status
- **Company Profiles** - Browse company profiles and their job listings
- **Resume Management** - Upload and manage resume files (PDF, DOCX supported)

### For Employers
- **Job Posting** - Create and manage job listings
- **Application Management** - Review and manage job applications
- **Company Management** - Create and update company profiles
- **Dashboard Analytics** - View statistics and insights
- **Application Status Updates** - Accept or reject applications

### For Administrators
- **User Management** - Manage all users (job seekers, employers, admins)
- **Company Management** - Oversee all company profiles
- **Job Management** - Monitor and manage all job postings
- **Application Oversight** - View and manage all applications system-wide
- **Dashboard Statistics** - Comprehensive analytics and reporting
- **Role-Based Access Control** - Manage user roles and permissions

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher (or MariaDB 10.2+)
- **Web Server** - Apache/Nginx (XAMPP, WAMP, MAMP, or LAMP)
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - fileinfo (for file uploads)

## 🛠️ Installation & Setup

### 1. Clone or Download the Project

```bash
# If using git
git clone <repository-url>
cd jobhub1

# Or download and extract to your web server directory
# For XAMPP: C:\xampp\htdocs\jobhub1 (Windows) or /Applications/XAMPP/xamppfiles/htdocs/jobhub1 (Mac)
```

### 2. Configure Database

Copy the database configuration template and update with your credentials:

```bash
cp config/database.php.example config/database.php
```

Then edit `config/database.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'jobhub');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password
```

### 3. Initialize Database

Run the database initialization script through your web browser:

```
http://localhost/jobhub1/setup.php
```

Or use the alternative initialization script:

```
http://localhost/jobhub1/init-database.php
```

This will:
- Create the `jobhub` database
- Create all required tables (users, companies, jobs, applications, skills, etc.)
- Insert default admin user
- Add sample data (optional, depending on script used)

### 4. Set Permissions

Ensure the `uploads/resumes/` directory is writable:

```bash
chmod 755 uploads/resumes/
```

### 5. Access the Application

Open your web browser and navigate to:

```
http://localhost/jobhub1/
```

## 🔐 Default Admin Credentials

After database initialization, you can login with:

- **Email:** `admin@jobhub.com` (or `admin@gmail.com` depending on initialization script)
- **Password:** `admin123`

**⚠️ Important:** Change the default admin password after first login!

## 📁 Project Structure

```
jobhub1/
├── admin/                      # Admin panel
│   ├── index.php              # Admin dashboard router
│   ├── dashboard-main.php     # Dashboard statistics
│   ├── users.php              # User management
│   ├── user-edit.php          # Edit user
│   ├── jobs.php               # Job management
│   ├── job-add.php            # Add new job
│   ├── job-edit.php           # Edit job
│   ├── applications.php       # Application management
│   ├── application-view.php   # View application details
│   ├── application-update-status.php  # Update application status
│   ├── companies.php          # Company management
│   ├── company-form.php       # Add/Edit company
│   ├── companies-list.php     # Company listing
│   ├── stats-api.php          # Statistics API endpoint
│   └── delete.php             # Delete operations
├── config/
│   └── database.php           # Database configuration
├── includes/
│   └── functions.php          # Core functions and utilities
├── theme/
│   ├── css/
│   │   └── modern.css         # Custom styles
│   └── templates.php          # Main template/layout
├── uploads/
│   └── resumes/               # Uploaded resume files
├── index.php                  # Homepage
├── home.php                   # Homepage content
├── jobs.php                   # Job listings page
├── jobs-content.php           # Job listings content
├── job-details.php            # Job detail page
├── job-details-content.php    # Job detail content
├── companies.php              # Company listings
├── company-content.php        # Company listings content
├── company-details.php        # Company detail page
├── company-details-content.php # Company detail content
├── apply.php                  # Job application page
├── applications.php           # User applications page
├── applications-content.php   # Applications content
├── login.php                  # Login page
├── register.php               # Registration page
├── logout.php                 # Logout handler
├── about.php                  # About page
├── about-content.php          # About content
├── contact.php                # Contact page
├── contact-content.php        # Contact content
├── setup.php                  # Database setup script
├── init-database.php          # Alternative database initialization
├── ajax_login.php             # AJAX login handler
├── ajax_login.js              # Login JavaScript
└── README.md                  # This file
```

## 🗄️ Database Schema

### Tables

- **users** - User accounts (job seekers, employers, admins)
- **companies** - Company profiles
- **jobs** - Job postings
- **applications** - Job applications
- **skills** - Skills database
- **job_skills** - Job-skill relationships
- **user_skills** - User-skill relationships

### User Roles

- **user** - Regular job seeker (default)
- **employer** - Can post jobs and manage applications
- **admin** - Full system access

## 🔑 Key Features Explained

### Authentication System
- Session-based authentication
- Password hashing with PHP `password_hash()`
- Role-based access control
- Protected routes for authenticated users

### File Uploads
- Resume uploads stored in `uploads/resumes/`
- Supported formats: PDF, DOCX
- Unique file naming to prevent conflicts
- File validation and error handling

### Job Management
- Job types: Full-time, Part-time, Contract, Internship
- Experience levels: Entry, Mid, Senior, Executive
- Salary ranges with min/max values
- Job status: Active, Inactive, Closed

### Application System
- Application status: Pending, Accepted, Rejected
- Resume and cover letter support
- One application per user per job (unique constraint)
- Application tracking and history

## 🎨 Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+ (PDO)
- **Frontend:** HTML5, CSS3, JavaScript
- **CSS Framework:** Bootstrap 3.4.1
- **Icons:** Font Awesome 4.7.0
- **Data Tables:** DataTables jQuery plugin
- **Animations:** AOS (Animate On Scroll)
- **Slider:** FlexSlider

## 🔧 Configuration

### Base URL Configuration

Update `BASE_URL` in `config/database.php` if your application is not in the root:

```php
define('BASE_URL', '/jobhub1/');  // Change this to match your setup
```

### File Upload Configuration

Maximum upload size is controlled by PHP settings. Update `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

## 🚨 Security Considerations

1. **Change Default Credentials** - Always change default admin password
2. **Database Password** - Use strong database passwords in production
3. **Session Security** - Implement session timeout and regenerate session IDs
4. **File Upload Validation** - Validate file types and sizes
5. **SQL Injection** - Uses PDO prepared statements (already implemented)
6. **XSS Protection** - Implement input sanitization (consider adding more)
7. **CSRF Protection** - Consider adding CSRF tokens for forms
8. **HTTPS** - Use HTTPS in production environment

## 🐛 Troubleshooting

### Database Connection Error
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database user has proper permissions

### File Upload Not Working
- Check `uploads/resumes/` directory permissions (755 or 777)
- Verify PHP upload settings in `php.ini`
- Check file size limits

### Page Not Found (404)
- Verify `.htaccess` rules (if using Apache)
- Check `BASE_URL` configuration
- Ensure mod_rewrite is enabled (if using URL rewriting)

### Session Issues
- Ensure `session_start()` is called before any output
- Check PHP session configuration
- Clear browser cookies

## 📝 Development Notes

- The application uses a template-based architecture
- Content pages are separated from layout templates
- Functions are centralized in `includes/functions.php`
- Admin panel uses a simple router pattern in `admin/index.php`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available for educational and commercial use.

## 👤 Support

For issues, questions, or contributions, please open an issue in the repository or contact the development team.

## 🎯 Future Enhancements (Potential)

- Email notifications for applications
- Advanced search and filtering
- User profile pages
- Saved jobs/bookmarks
- Job recommendations
- API endpoints
- RESTful API implementation
- OAuth integration
- Two-factor authentication
- Advanced analytics and reporting
- Export functionality (PDF reports, CSV exports)
- Multi-language support

---

**Built with ❤️ for connecting talents with opportunities**
