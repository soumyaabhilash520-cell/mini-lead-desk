LeadDesk Mini – PHP + MySQL Edition

A modern, production-ready Lead Capture & Management System built with PHP 8, MySQL, HTML5, CSS3, Bootstrap 5.3, and Vanilla JavaScript.

The application includes a public lead capture landing page, secure admin authentication, lead management dashboard, real-time analytics, AJAX-based interactions, CSV export, and responsive UI designed with modern SaaS aesthetics.

------------------------------------------------------------

1. Project Overview

LeadDesk Mini is designed as a lightweight CRM for businesses to collect and manage leads efficiently.

The application consists of two main sections:

1. Public Landing Page
2. Admin Dashboard

Visitors can submit inquiries through the landing page while administrators can securely manage, update, search, filter, export, and analyze all submitted leads from the dashboard.

------------------------------------------------------------

2. Core Features

2.1 Public Landing Page

The public landing page serves as the marketing and lead capture interface.

Features include:

• Responsive SaaS-style design
• Glassmorphism UI
• Bootstrap 5 responsive layout
• Gradient color system
• Mobile-friendly interface
• Smooth animations
• Contact form
• AJAX form submission
• Success toast notifications
• Footer credit linking to Digital Heroes

Collected fields:

• Full Name
• Email Address
• Budget Range
• Project Message

Budget Options

• Under $500
• $500 – $1,000
• $1,000 – $5,000
• Over $5,000

------------------------------------------------------------

2.2 Client Side Validation

JavaScript performs instant validation before submission.

Validation includes:

• Required field validation
• Email format validation
• Character count
• Minimum message length
• Maximum message length
• Live validation feedback
• Loading button state
• Prevent duplicate submissions

------------------------------------------------------------

2.3 Server Side Validation

PHP validates every request regardless of browser validation.

Security includes:

• Input sanitization
• XSS prevention
• CSRF verification
• Email validation
• Length validation
• Required field validation

------------------------------------------------------------

2.4 Secure Admin Authentication

The admin area is protected using PHP sessions.

Authentication features:

• Secure login
• Logout
• Session protection
• Route protection
• Password hashing
• Password verification
• Unauthorized redirect

Default Login

Email

admin@leaddesk.com

Password

admin123

------------------------------------------------------------

2.5 Dashboard

The dashboard provides a complete overview of all lead activities.

Statistics Cards

• Total Leads
• New Leads
• Contacted Leads
• Closed Leads

Analytics

• Status Doughnut Chart
• Budget Distribution Chart

Lead Management

• Search leads
• Filter leads
• View lead details
• Update status
• Delete leads
• Export CSV

------------------------------------------------------------

2.6 AJAX Features

Several operations work without refreshing the page.

Included AJAX modules:

• Lead submission
• Lead search
• Status updates
• Live filtering
• Modal loading
• Statistics refresh

------------------------------------------------------------

2.7 Search and Filtering

Real-time searching supports:

• Name
• Email

Available Filters

• All
• New
• Contacted
• Closed

------------------------------------------------------------

2.8 Lead Details

Each lead can be viewed inside a modal displaying:

• Full Name
• Email
• Budget
• Message
• Current Status
• Created Date
• Updated Date

------------------------------------------------------------

2.9 CSV Export

Administrators can export:

• All Leads
• Filtered Leads

CSV contains:

• Name
• Email
• Budget
• Message
• Status
• Created Date
• Updated Date

------------------------------------------------------------

2.10 Dark Mode

The dashboard supports both light and dark themes.

Features include:

• One-click toggle
• LocalStorage persistence
• Automatic UI switching

------------------------------------------------------------

3. Technology Stack

Frontend

• HTML5
• CSS3
• Bootstrap 5.3
• Vanilla JavaScript (ES6)
• Font Awesome 6
• Chart.js

Backend

• PHP 8+
• PDO
• Session Authentication

Database

• MySQL
• MariaDB

Development Environment

• Apache
• XAMPP
• WAMP
• Laragon

------------------------------------------------------------

4. Project Structure

minidesk/

index.php
Public Landing Page

login.php
Admin Login

logout.php
Logout Handler

dashboard.php
Admin Dashboard

leads.php
Lead Management

submit-lead.php
Lead Submission Endpoint

update-status.php
AJAX Status Update

export-leads.php
CSV Export

delete-lead.php
Delete Lead

config/

database.php
PDO Connection

auth.php
Authentication

includes/

header.php

footer.php

navbar.php

sidebar.php

functions.php

assets/

css/

style.css

js/

main.js

dashboard.js

images/

sql/

database.sql

README.md

------------------------------------------------------------

5. Installation

Step 1

Copy the project folder into:

C:\xampp\htdocs\minidesk

Step 2

Open XAMPP.

Start:

• Apache
• MySQL

Step 3

Database Setup

Option 1

Open the application.

The database installer automatically creates:

• Database
• Tables
• Default Admin

Option 2

Import manually using MySQL CLI.

mysql -u root < sql/database.sql

Option 3

Import using phpMyAdmin.

Create database:

leaddesk

Import:

sql/database.sql

------------------------------------------------------------

6. Access URLs

Public Website

http://localhost/minidesk/index.php

Admin Login

http://localhost/minidesk/login.php

------------------------------------------------------------

7. Database Design

7.1 Admins Table

Columns

id
Primary Key

email
Unique Email

password
Bcrypt Password Hash

created_at
Created Timestamp

------------------------------------------------------------

7.2 Leads Table

Columns

id
Primary Key

name
Lead Name

email
Lead Email

budget
Selected Budget

message
Lead Message

status

Possible Values

• New
• Contacted
• Closed

created_at

updated_at

------------------------------------------------------------

8. Security Features

8.1 SQL Injection Protection

All queries use PDO prepared statements with parameter binding.

8.2 Cross Site Scripting Protection

Every output is escaped using:

htmlspecialchars()

8.3 CSRF Protection

Forms and AJAX requests require secure CSRF tokens validated using:

hash_equals()

8.4 Password Security

Passwords are stored using:

password_hash()

Passwords are verified using:

password_verify()

8.5 Session Protection

Only authenticated administrators can access:

• dashboard.php
• leads.php

Unauthorized users are redirected to:

login.php

------------------------------------------------------------

9. Deployment Guide

Step 1

Upload all files into:

public_html/

Step 2

Create a MySQL database.

Step 3

Import:

sql/database.sql

Step 4

Update config/database.php with production credentials.

Example

DB_HOST

DB_USER

DB_PASS

DB_NAME

------------------------------------------------------------

10. Application Workflow

Visitor opens landing page.

↓

Visitor fills lead form.

↓

JavaScript validates inputs.

↓

AJAX submits data.

↓

PHP validates request.

↓

Lead is stored in MySQL.

↓

Administrator logs in.

↓

Dashboard loads analytics.

↓

Administrator searches or filters leads.

↓

Administrator updates lead status.

↓

Charts update automatically.

↓

Administrator exports CSV if required.

------------------------------------------------------------

11. Folder Summary

Root Files

• Landing Page
• Login
• Dashboard
• Lead APIs
• Export
• Delete

Config

• Database Connection
• Authentication

Includes

• Header
• Footer
• Navbar
• Sidebar
• Utility Functions

Assets

• CSS
• JavaScript
• Images

SQL

• Database Schema
• Sample Data

------------------------------------------------------------

12. Default Administrator

Email

admin@leaddesk.com

Password

admin123

------------------------------------------------------------

13. Key Highlights

• Modern SaaS UI
• Responsive Design
• Bootstrap 5.3
• PHP 8
• MySQL Database
• PDO Prepared Statements
• Session Authentication
• Password Hashing
• CSRF Protection
• XSS Protection
• AJAX Lead Submission
• AJAX Status Updates
• Live Search
• Live Filtering
• Chart.js Analytics
• CSV Export
• Dark Mode
• Mobile Friendly
• Production Ready

------------------------------------------------------------

14. Footer Requirement

The application footer displays the following credit exactly as required:

Built for Digital Heroes Training Task

The credit links directly to:

https://digitalheroesco.com