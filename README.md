# Student Management & Attendance System

A comprehensive web-based system for managing students, teachers, and attendance in a coaching institute.

## Features

### Admin Features
- Dashboard with statistics and charts
- Add, edit, delete students and teachers
- Mark attendance for any teacher/subject
- View comprehensive attendance reports
- Export reports to CSV
- Post announcements to students/teachers
- Manage user credentials

### Teacher Features
- Personal dashboard with statistics
- Mark attendance for their classes
- View attendance reports
- See assigned students
- View attendance trends

### Student Features
- Personal dashboard with attendance statistics
- View complete attendance history
- View personal details
- Read announcements from admin

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Charts**: Chart.js
- **Server**: XAMPP/WAMP or any PHP server

## Installation Instructions

### Prerequisites
1. XAMPP/WAMP installed (or any PHP + MySQL server)
2. PHP 7.4 or higher
3. MySQL 5.7 or higher
4. Web browser (Chrome, Firefox, Edge, Safari)

### Step 1: Setup XAMPP
1. Download and install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Create Project Directory
1. Navigate to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
2. Create a new folder named `coaching_system`
3. Copy all project files into this folder

### Step 3: Project Structure
```
coaching_system/
├── config/
│   └── db.php
├── api/
│   ├── students.php
│   ├── teachers.php
│   ├── attendance.php
│   ├── announcements.php
│   └── chart_data.php
├── css/
│   └── style.css
├── js/
│   ├── main.js
│   ├── students.js
│   ├── teachers.js
│   ├── attendance.js
│   ├── report.js
│   ├── announcements.js
│   └── charts.js
├── includes/
│   └── navbar.php
├── database.sql
├── login.php
├── logout.php
├── admin_dashboard.php
├── teacher_dashboard.php
├── student_dashboard.php
├── manage_students.php
├── manage_teachers.php
├── mark_attendance.php
├── view_report.php
├── announcements.php
└── README.md
```

### Step 4: Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin/
2. Click on "New" to create a new database
3. Name it `coaching_institute`
4. Click on the database name
5. Go to "Import" tab
6. Choose the `database.sql` file
7. Click "Go" to import

**OR** run the SQL commands directly:
1. Open phpMyAdmin SQL tab
2. Copy and paste contents from `database.sql`
3. Click "Go"

### Step 5: Configure Database Connection
Open `config/db.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for default XAMPP
define('DB_NAME', 'coaching_institute');
```

### Step 6: Access the System
1. Open your browser
2. Navigate to: http://localhost/coaching_system/login.php
3. Use default credentials to login

## Default Login Credentials

### Admin Account
- **Email**: admin@coaching.com
- **Password**: password

### Teacher Account
- **Email**: john@coaching.com
- **Password**: password

### Student Account
- **Email**: sarah@coaching.com
- **Password**: password

## Usage Guide

### For Admin

1. **Managing Students**
   - Go to "Students" from navigation
   - Click "Add New Student" button
   - Fill in student details
   - Click "Add Student"

2. **Managing Teachers**
   - Go to "Teachers" from navigation
   - Click "Add New Teacher" button
   - Fill in teacher details
   - Click "Add Teacher"

3. **Marking Attendance**
   - Go to "Mark Attendance"
   - Select date, teacher, and subject
   - Mark students as Present/Absent
   - Click "Submit Attendance"

4. **Viewing Reports**
   - Go to "Reports"
   - Apply filters (student, date range, subject)
   - Click "Generate Report"
   - Export to CSV if needed

5. **Posting Announcements**
   - Go to "Announcements"
   - Click "Add Announcement"
   - Enter title, message, and target audience
   - Click "Post Announcement"

### For Teachers

1. **Marking Attendance**
   - Go to "Mark Attendance"
   - Select date (subject auto-filled)
   - Mark students as Present/Absent
   - Click "Submit Attendance"

2. **Viewing Reports**
   - Go to "View Reports"
   - Apply filters as needed
   - Generate and export reports

### For Students

1. **Viewing Attendance**
   - Dashboard shows attendance summary
   - Go to "My Attendance" for detailed view
   - Check recent attendance records

2. **Reading Announcements**
   - Go to "Announcements"
   - View all posted announcements

## Features Implemented

✅ Role-based authentication (Admin, Teacher, Student)  
✅ Session-based security  
✅ CRUD operations for students and teachers  
✅ Attendance marking with date and subject  
✅ Attendance reports with filters  
✅ CSV export functionality  
✅ Dashboard with statistics  
✅ Attendance trend charts (Chart.js)  
✅ Announcement system  
✅ Responsive design  
✅ Form validation (JavaScript)  
✅ Success/Error notifications  
✅ Modern UI with light theme  

## Security Features

- Password hashing using PHP's `password_hash()`
- Session-based authentication
- SQL injection prevention using prepared statements
- XSS prevention with input sanitization
- Role-based access control
- CSRF protection ready

## Browser Support

- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari
- Opera

## Troubleshooting

### Database Connection Error
- Check if MySQL service is running
- Verify database credentials in `config/db.php`
- Ensure database is created and imported

### Login Not Working
- Clear browser cache and cookies
- Check if sessions are enabled in PHP
- Verify user exists in database

### Attendance Not Submitting
- Check browser console for JavaScript errors
- Verify API files are in correct location
- Check database permissions

### Charts Not Displaying
- Ensure internet connection (Chart.js loads from CDN)
- Check browser console for errors
- Verify chart_data.php is accessible

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review PHP error logs: `C:\xampp\apache\logs\error.log`
3. Check browser console for JavaScript errors

## Future Enhancements

- Email notifications
- SMS integration
- PDF report generation
- Biometric attendance integration
- Mobile app
- Parent portal
- Fee management
- Online classes integration

## License

This project is created for educational purposes.

## Credits

Developed for Coaching Institute Management  
Version: 1.0  
Last Updated: November 2025