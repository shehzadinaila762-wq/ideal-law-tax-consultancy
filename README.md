# Ideal Law and Tax Consultancy

A responsive website developed for Ideal Law and Tax Consultancy.

## Technologies Used
- HTML
- CSS
- PHP
- MySQL

## Main Features
- Responsive website
- Services and About sections
- Gallery
- Team Members
- Contact Query Form
- Admin Login
- Client Management
- Gallery Management
- Team Member Management
- CRUD Operations
- MySQL Database

## Database
Database Name: `ideal_law`

## Project Structure
```
ideal-law-tax-consultancy/
├── public/              # web root — point your server here
│   ├── *.php            # entry points (index, about, services, admin, ...)
│   ├── assets/
│   │   ├── css/         # index.css
│   │   └── images/      # static images (logo, etc.)
│   └── uploads/         # gallery/member images uploaded via admin panel
├── config/
│   └── database.php     # single mysqli connection, required by every DB-backed page
├── database/
│   └── ideal_law.sql    # schema + seed data dump
└── README.md
```

## Setup Guide

### Prerequisites
- PHP 8.x (with the `mysqli` extension)
- MySQL / MariaDB server

### 1. Clone the repository
```bash
git clone <repo-url>
cd ideal-law-tax-consultancy
```

### 2. Start MySQL
Make sure your MySQL server is running.

### 3. Create the database and import the schema
```bash
mysql -u root -p -e "CREATE DATABASE ideal_law;"
mysql -u root -p ideal_law < database/ideal_law.sql
```
This creates the `clients`, `gallery`, `payments`, `queries`, and `team_members` tables.

### 4. Configure the database connection
All DB-backed pages (`index.php`, `admin.php`, `contact.php`, `gallery.php`, `team.php`) pull a single shared connection from `config/database.php`:
```php
$conn = new mysqli("localhost", "root", "", "ideal_law");
```
Update the host/user/password there to match your local MySQL setup if it differs from `root` with no password.

### 5. Run the app
Using PHP's built-in server, pointed at the `public/` web root:
```bash
php -S localhost:8000 -t public
```
Then open [http://localhost:8000/index.php](http://localhost:8000/index.php) in your browser.

Alternatively, point your web server's document root (e.g. XAMPP/MAMP `htdocs`, or an Apache/Nginx vhost) at the `public/` folder and access it via `http://localhost/index.php`.

### Notes
- The default DB credentials (`root` / no password) are intended for local development only — do not use them in production.
- Admin panel is available at `admin.php` (requires login via `login.php`).

## Project Purpose
The purpose of this project is to provide a professional online presence for Ideal Law and Tax Consultancy and to allow the administrator to manage website content and client information through an Admin Panel.
