DevConnect - Project README

Overview:
---------
DevConnect is a platform designed to connect clients with developers. It features user registration (clients, developers, admin), real-time chat, job posting, resume uploads, and an administrative panel for system management.

Features:
---------
- User Roles: Developer, Client, Admin
- Registration & Login with role-based redirects
- Developer Profile: add skills, upload projects, delete account
- Client Dashboard: browse developers, post jobs, delete account
- Real-time Chat: one-on-one messaging with read receipts
- Job Posting: clients can post and manage jobs
- Administrative Panel: manage users, jobs, notifications
- Dark Mode Toggle and Responsive UI
- Security: account lockout after multiple failed login attempts

Skipped Features:
-----------------
- Notification sound on message receive
- Delete skill card in UI (handled inline)
- Group chat system
- Enhanced themed notification page

Requirements:
-------------
- PHP 7.4+ with mysqli and sessions enabled
- MySQL / MariaDB database
- Apache/Nginx web server
- Composer (optional for dependencies)
- Sound file for notifications (optional)

Installation:
-------------
1. Clone repository:
   git clone <repository-url>
2. Configure database:
   - Create MySQL database 'devconnect'
   - Run provided SQL schema (tables: users, messages, skills, projects, jobs, login_attempts, resumes)
3. Update 'includes/db.php' with database credentials
4. Place 'sound/notification.mp3' if using audio notifications
5. Start web server and navigate to project folder

Usage:
------
- Register as Developer, Client, or Admin
- Developers manage their profile and chat with clients
- Clients browse developers, post jobs, and chat
- Admin logs in to admin_login.php to manage users and jobs

Directory Structure:
--------------------
/css
/js
/images
/includes
   db.php
   other includes
/profile_developer.php
/profile_client.php
/admin_dashboard.php
/others...

Screenshots:
------------
Please refer to 'docs/Screenshots' folder for UI screenshots:
- Login Page
- Registration Page
- Developer Dashboard
- Client Dashboard
- Admin Panel

License:
--------
MIT License

Author:
-------
Development Team
Badar Bhai
