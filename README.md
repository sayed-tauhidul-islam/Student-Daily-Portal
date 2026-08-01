# Student Daily Portal

Student Daily Portal is a role-based school operations portal for students, teachers, head teachers, and platform administrators. It is built with Laravel, MongoDB, Blade, Tailwind CSS, Alpine.js, and Vite.

The application is designed for school and college workflows in Bangladesh, with separate dashboards, authentication guards, navigation menus, and data access rules for each role.

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [User Roles](#user-roles)
- [Technology Stack](#technology-stack)
- [Database](#database)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Environment Configuration](#environment-configuration)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [Security and Access Control](#security-and-access-control)
- [Documentation Assets](#documentation-assets)
- [Author](#author)
- [License](#license)

## Overview

Student Daily Portal helps schools manage day-to-day academic and administrative work from one web application. Each portal is scoped to the logged-in user's role, so students, teachers, head teachers, and administrators only see the tools and records relevant to them.

The system supports student profiles, teacher profiles, school-scoped rosters, attendance, notices, student progress, payments, complaints, leave applications, reading logs, messaging, login review, and admin-level school management.

## Key Features

- Multi-role authentication for students, teachers, head teachers, and administrators
- Role-aware dashboards and navigation
- School-scoped student and teacher management
- Attendance create, update, and delete workflows
- Student progress tracking with subject performance and exam result records
- Tuition fee and payment confirmation workflows
- Institute notices and student-specific notices
- Student tasks, goals, assignments, and progress hub
- Teacher posts and student tuition requests
- School member messaging
- Complaint and leave review workflows
- Login review, blocking, and account control for school authorities
- Responsive Blade and Tailwind CSS interface

## User Roles

### Student

Students can manage their profile, view attendance, read notices, browse institute teachers, submit requests, track reading logs, submit payment confirmations, view progress, manage tasks, and access school communication tools.

### Teacher

Teachers can manage their professional profile, create posts, review student requests, manage attendance, publish notices, update student progress, and communicate with school members.

### Head Teacher

Head teachers manage records for their own school only. They can control student and teacher rosters, manage student attendance, update progress, approve or edit monthly fees, send student-specific notices or tasks, review complaints and leaves, inspect school messages, and manage login reviews.

### Administrator

Administrators manage platform-level records such as schools, students, teachers, subjects, groups, ratings, and system-wide login review.

## Technology Stack

### Backend

- PHP 8.3+
- Laravel 13
- Laravel Breeze
- Laravel middleware and multi-guard authentication
- MongoDB Laravel driver: `mongodb/laravel-mongodb`

### Frontend

- Laravel Blade
- Tailwind CSS
- Alpine.js
- Vite

### Database Software

- MongoDB

### Development and Testing

- Composer
- npm
- PHPUnit
- Laravel Pint
- Laravel Pail

## Database

This project uses MongoDB as the primary database software.

The default database connection is configured as:

```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=student_daily_portal
DB_USERNAME=
DB_PASSWORD=
```

The MongoDB connection is defined in `config/database.php`, and the application models use MongoDB-backed collections through the Laravel MongoDB package.

Main data areas include:

- users
- students
- teachers
- schools
- subjects
- groups
- attendances
- notices
- student progress
- student tasks
- messages
- payment confirmations
- complaints
- leave applications
- login reviews

## Project Structure

```text
app/
  Http/
    Controllers/
      Admin/
      Auth/
      Frontend/
      Student/
      Teacher/
      TeacherAdmin/
    Middleware/
    Requests/
  Models/
  Notifications/
  Providers/
  Support/

config/
database/
docs/
public/
resources/
  css/
  js/
  views/
routes/
screenshots/
storage/
tests/
```

Important files:

- `routes/web.php` - main web routes and portal route groups
- `routes/auth.php` - authentication routes
- `config/auth.php` - guard and provider configuration
- `config/database.php` - MongoDB and other database connection settings
- `resources/views/layouts/app.blade.php` - authenticated application layout
- `resources/views/layouts/navigation.blade.php` - shared navigation shell
- `app/Http/Controllers/SchoolPortalController.php` - shared school portal workflows
- `app/Http/Controllers/TeacherAdmin/TeacherAdminStudentController.php` - head teacher student management

## Installation

### Requirements

- PHP 8.3 or later
- Composer
- Node.js 18 or later
- npm
- MongoDB server
- PHP MongoDB extension

### Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure MongoDB in `.env`, then run:

```bash
php artisan migrate
php artisan db:seed
```

## Environment Configuration

Recommended local `.env` values:

```env
APP_NAME="Student Daily Portal"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=student_daily_portal
DB_USERNAME=
DB_PASSWORD=

FILESYSTEM_DISK=public
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Generate the application key after copying the environment file:

```bash
php artisan key:generate
```

## Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

Start the Vite development server:

```bash
npm run dev
```

For a production-ready frontend build:

```bash
npm run build
```

The project also includes a Composer development script that can run the Laravel server, queue listener, logs, and Vite together:

```bash
composer run dev
```

## Testing

Run the full test suite:

```bash
php artisan test
```

Run selected feature tests:

```bash
php artisan test tests/Feature/HeadTeacherPanelTest.php
php artisan test tests/Feature/MultiGuardSessionTest.php
php artisan test tests/Feature/AdminRosterFilterTest.php
```

Clear cached configuration and views if the application behaves unexpectedly during development:

```bash
php artisan optimize:clear
```

## Security and Access Control

- Separate guards for student, teacher, teacher admin, and admin accounts
- Role middleware for protected routes
- Portal-aware dashboard redirects
- CSRF protection on forms
- Laravel password hashing
- School-level ownership checks for head teacher workflows
- Student-specific visibility for private notices and payment records
- Guard-aware profile updates and logout behavior

## Documentation Assets

Additional project diagrams are available in:

```text
docs/er-diagram.mmd
docs/use-case-diagram.mmd
```

UI screenshots are stored in:

```text
screenshots/
```

## Maintenance Notes

- Keep `.env` out of version control.
- Run tests after changing role, guard, payment, attendance, or school-scoped logic.
- Use `php artisan optimize:clear` after route, config, or Blade cache issues.
- Use MongoDB-compatible model patterns when adding new collections.

## Author

Sayed Tauhidul Islam

## License

This project is maintained for academic and product development purposes.
