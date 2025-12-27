# GearGuard - Maintenance Tracker System

A comprehensive web-based maintenance management system built with PHP and MySQL for tracking equipment maintenance requests, teams, and workflows.

## 🚀 Features

### User Management
- **Multi-role authentication** (Admin, Manager, Technician, Employee)
- **Department-based organization**
- **User profiles and avatars**
- **Role-based access control**

### Equipment Management
- **Equipment registration** with serial numbers and warranty tracking
- **Department assignment** and user allocation
- **Location tracking**
- **Equipment lifecycle management** (including scrap status)

### Maintenance Workflow
- **Request creation** (Corrective and Preventive maintenance)
- **Team assignment** and technician allocation
- **Status tracking** (New → In Progress → Repaired/Scrap)
- **Kanban board** for visual workflow management
- **Calendar scheduling** for maintenance activities

### Reporting & Analytics
- **Dashboard metrics** for open and completed requests
- **Request logs** with full audit trail
- **Team performance tracking**
- **Equipment maintenance history**

## 📋 System Requirements

- **Web Server**: Apache/Nginx
- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Browser**: Modern web browser with JavaScript enabled

## 🛠️ Installation

### 1. Clone/Download the Project
```bash
git clone <repository-url>
cd gearguard
```

### 2. Database Setup
1. Create a MySQL database named `gearguard`
2. Import the database schema:
   ```bash
   mysql -u root -p gearguard < sql/gearguard.sql
   ```

### 3. Configuration
1. Update database credentials in `config/db.php`:
   ```php
   $DB_HOST = "localhost";
   $DB_USER = "your_username";
   $DB_PASS = "your_password";
   $DB_NAME = "gearguard";
   ```

### 4. Web Server Setup
- Place the project files in your web server's document root
- Ensure PHP has write permissions for session handling
- Configure virtual host (optional but recommended)

## 🏗️ Project Structure

```
gearguard/
├── assets/                 # Static assets (CSS, JS)
├── auth/                   # Authentication modules
│   ├── login.php          # User login
│   ├── signup.php         # User registration
│   └── logout.php         # Session termination
├── config/                 # Configuration files
│   ├── db.php             # Database connection
│   ├── auth.php           # Authentication middleware
│   └── constants.php      # System constants
├── dashboard/              # Dashboard and analytics
│   ├── dashboard.php      # Main dashboard
│   ├── kanban.php         # Kanban board view
│   └── calendar.php       # Calendar scheduling
├── equipment/              # Equipment management
├── requests/               # Maintenance requests
├── teams/                  # Team management
├── users/                  # User management
├── reports/                # Reporting modules
├── logs/                   # System logs
├── includes/               # Shared components
└── sql/                    # Database schema
```

## 👥 User Roles & Permissions

### Admin
- Full system access
- User and team management
- System configuration
- All reports and analytics

### Manager
- Department-level access
- Create and manage requests
- View team performance
- Equipment oversight

### Technician
- Kanban board access
- Update request status
- Log maintenance activities
- View assigned tasks

### Employee
- Create maintenance requests
- View own requests
- Basic equipment information

## 🔧 Usage

### Getting Started
1. Access the application via your web browser
2. Login with your credentials
3. You'll be redirected based on your role:
   - **Technicians**: Kanban board
   - **Others**: Main dashboard

### Creating Maintenance Requests
1. Navigate to **Requests → Create New**
2. Select equipment and request type
3. Set priority and schedule date
4. Submit for team assignment

### Managing Workflow (Technicians)
1. Access the **Kanban Board**
2. Drag requests between status columns
3. Add notes and update progress
4. Mark as completed when finished

### Monitoring (Managers/Admins)
1. Use the **Dashboard** for overview metrics
2. Check **Reports** for detailed analytics
3. Monitor team performance
4. Review equipment maintenance history

## 🔐 Security Features

- **Session-based authentication**
- **Role-based access control**
- **SQL injection prevention** (prepared statements)
- **Input validation and sanitization**
- **Secure password handling**

## 📊 Database Schema

The system uses 7 main tables:
- `users` - User accounts and roles
- `departments` - Organizational departments
- `equipment` - Equipment registry
- `maintenance_teams` - Maintenance teams
- `maintenance_requests` - Service requests
- `request_logs` - Audit trail
- `maintenance_team_members` - Team assignments

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Check the documentation
- Review the code comments
- Create an issue in the repository

## 🔄 Version History

- **v1.0.0** - Initial release with core functionality
- Basic CRUD operations
- Role-based authentication
- Kanban workflow
- Reporting dashboard

---

**GearGuard** - Streamlining maintenance operations with intelligent workflow management.