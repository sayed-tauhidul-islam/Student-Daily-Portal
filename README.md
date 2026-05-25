# TutorLink BD - Student Daily Portal

A role-based academic management platform for schools and colleges in Bangladesh. The system supports students, teachers, head sir (teacher admin), and super admin with dedicated dashboards, school-scoped controls, attendance tracking, notices, and profile management.

## Can This Project Track Student Progress?
Yes. This project can track student progress in practical ways through:
- Attendance history and teacher-managed attendance records.
- Profile completeness and student academic profile data.
- Class and school-wise student visibility for teachers/head sir.
- Notice and request workflows that show student engagement.

Note: Advanced analytics (charts, GPA trends, exam-result intelligence) are not yet implemented, but the current data model is ready for those future modules.

## Project Summary
TutorLink BD Student Daily Portal is built for school-level daily operations where:
- Students manage profiles, follow notices, view attendance, and discover institute teachers.
- Teachers manage attendance, notices, teaching posts, and class-related visibility.
- Head sir (teacher admin) controls only their own school database (students + teachers).
- Super admin manages global school, user, subject, and system entities.

## Tech Stack

### Frontend
- Blade templates
- Tailwind CSS
- Alpine.js (interactive UI behavior)
- Vite asset pipeline

### Backend
- PHP 8+
- Laravel framework
- Role-based middleware and controller architecture
- Notification and request flow with Laravel services

### Database
- MongoDB (via Laravel MongoDB driver)
- Collections include: `users`, `students`, `teachers`, `subjects`, `attendance`, `notices`, `ratings`, `student_requests`, `teacher_posts`, `schools`, `notifications`

### Design System
- Responsive dashboard-first layout
- Professional teal-slate color system
- Role-adaptive navigation and sidebar behavior
- Mobile-friendly interactions for key flows

## Core Features

### Authentication & Roles
- Registration and login for multiple roles
- Role-based redirection to correct dashboard
- Protected routes via middleware

### Student Panel
- Complete and update profile (school, class, group, subjects, image, area, bio, phone)
- Conditional class/group behavior (group for classes 9-12)
- View attendance history
- View institute teachers
- View notices

### Teacher Panel
- Teacher profile management
- Create and manage notices
- Attendance management for students
- Tuition post creation and request handling
- Class-wise student count visibility by school

### Head Sir (Teacher Admin) Panel
- School-scoped dashboard
- Add/edit/delete teachers
- Add/edit/delete students
- School Database view (teachers + students + linked user info)
- Scope protection so only assigned school data is manageable

### Super Admin Panel
- Manage schools
- Manage students and teachers
- Manage subjects, groups, ratings
- Overall system-level administration

### Academic Data Support
- Bangladesh standard subjects seeded for class 1 to 12
- Class-level subject categorization support

## Project Structure
- `app/Http/Controllers` - role-wise controller logic
- `app/Models` - MongoDB Eloquent models
- `resources/views` - Blade UI for all panels
- `routes/web.php` - complete web routing
- `database/seeders` - initial academic and system data
- `docs` - ER/use-case diagram sources
- `screenshots` - UI preview assets

## Installation

### Requirements
- PHP 8.1+
- Composer
- Node.js + npm
- MongoDB server

### Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure MongoDB connection in `.env`, then run:
```bash
php artisan migrate
php artisan db:seed
npm run dev
php artisan serve
```

## Screenshots
- Home: `screenshots/home_page.png`
- Login: `screenshots/login_page.png`
- Register: `screenshots/register_page.png`
- Student dashboards: `screenshots/student_dashboard1.png` to `student_dashboard10.png`

## Security & Access Control
- CSRF protection
- Password hashing via Laravel
- Middleware-protected role routes
- School-level ownership checks for teacher admin operations

## Current Limitations
- No advanced progress analytics charts yet
- No exam-result module yet
- No mobile app client yet

## Future Roadmap
- Student progress analytics dashboard
- Exam result + performance trend modules
- Parent/guardian portal
- Real-time messaging improvements

## Author
- Sayed Tauhidul Islam
- NUBTK, CSE Department

## License
This project is maintained for academic and product development purposes.
