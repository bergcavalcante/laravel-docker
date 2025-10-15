# Task Management System - Proprli Assessment

A comprehensive Laravel 10 REST API for managing tasks in real estate buildings, designed for property management companies. This system allows property owners to create and manage tasks across multiple buildings, assign them to team members, and track progress through comments.

## Features

- **Multi-building Management**: Manage tasks across multiple real estate properties
- **User Roles**: Support for owners and team members with proper access control
- **Task Assignment**: Assign tasks to specific team members
- **Task Status Tracking**: Track tasks through their lifecycle (Open, In Progress, Completed, Rejected)
- **Comment System**: Add comments to tasks for progress tracking and communication
- **Advanced Filtering**: Filter tasks by status, assigned user, date ranges, and more
- **RESTful API Architecture**: Clean, well-documented API endpoints
- **Type Safety**: Full type hints on methods and parameters
- **Comprehensive Testing**: Unit and feature tests for reliability
- **PSR-12 Compliant**: Follows modern PHP coding standards

## Tech Stack

- **Laravel 10.x** - PHP Framework
- **PHP 8.2** - Programming Language
- **MySQL 8.0** - Database
- **Nginx** - Web Server
- **Docker & Docker Compose** - Containerization
- **Laravel Sanctum** - API Authentication
- **PHPUnit** - Testing Framework
- **PHP CodeSniffer** - Code Quality

## Requirements

- Docker Desktop
- Docker Compose
- Git

## Installation

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd laravel-docker
```

### 2. Start Docker Containers

```bash
docker-compose up -d
```

This will start three containers:
- `laravel_app` - PHP 8.2-FPM application server
- `laravel_db` - MySQL 8.0 database server
- `laravel_web` - Nginx web server

### 3. Install Dependencies

```bash
docker-compose exec app composer install
```

### 4. Configure Environment

The `.env` file should already be configured with the correct database settings:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### 5. Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### 6. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 7. (Optional) Seed Database

To populate the database with sample data:

```bash
docker-compose exec app php artisan db:seed
```

This will create:
- 1 owner account (email: owner@example.com, password: password)
- 7 team members (email: jane@example.com, bob@example.com, password: password)
- 3 buildings with various tasks
- Multiple tasks with different statuses
- Comments on tasks

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

### Authentication Endpoints

#### Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "owner",
  "account_id": null
}
```

**Response:** `201 Created`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "owner"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

#### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xyz789...",
  "token_type": "Bearer"
}
```

#### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "message": "Successfully logged out"
}
```

### Task Endpoints

#### List Building Tasks (with filters)

```http
GET /api/buildings/{building_id}/tasks
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional) - Filter by status: `open`, `in_progress`, `completed`, `rejected`
- `assigned_to` (optional) - Filter by assigned user ID
- `created_from` (optional) - Filter tasks created from this date (Y-m-d format)
- `created_to` (optional) - Filter tasks created until this date (Y-m-d format)
- `due_date_from` (optional) - Filter tasks due from this date
- `due_date_to` (optional) - Filter tasks due until this date

**Example:**
```bash
GET /api/buildings/1/tasks?status=open&assigned_to=2&created_from=2025-01-01
```

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "building_id": 1,
      "title": "Fix broken window",
      "description": "Window on 3rd floor needs repair",
      "status": "open",
      "due_date": "2025-10-20",
      "assigned_to": {
        "id": 2,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "team_member"
      },
      "created_by": {
        "id": 1,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "role": "owner"
      },
      "comments": [
        {
          "id": 1,
          "task_id": 1,
          "content": "Started working on this",
          "user": {
            "id": 2,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "team_member"
          },
          "created_at": "2025-10-13T10:00:00+00:00",
          "updated_at": "2025-10-13T10:00:00+00:00"
        }
      ],
      "created_at": "2025-10-13T09:00:00+00:00",
      "updated_at": "2025-10-13T09:00:00+00:00"
    }
  ]
}
```

#### Create Task

```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
  "building_id": 1,
  "title": "Fix broken window",
  "description": "Window on 3rd floor needs repair",
  "assigned_to": 2,
  "status": "open",
  "due_date": "2025-10-20"
}
```

**Response:** `201 Created`
```json
{
  "data": {
    "id": 1,
    "building_id": 1,
    "title": "Fix broken window",
    "description": "Window on 3rd floor needs repair",
    "status": "open",
    "due_date": "2025-10-20",
    "assigned_to": {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "team_member"
    },
    "created_by": {
      "id": 1,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "role": "owner"
    },
    "comments": [],
    "created_at": "2025-10-13T09:00:00+00:00",
    "updated_at": "2025-10-13T09:00:00+00:00"
  }
}
```

### Comment Endpoints

#### Create Comment

```http
POST /api/comments
Authorization: Bearer {token}
Content-Type: application/json

{
  "task_id": 1,
  "content": "Started working on this task"
}
```

**Response:** `201 Created`
```json
{
  "data": {
    "id": 1,
    "task_id": 1,
    "content": "Started working on this task",
    "user": {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "team_member"
    },
    "created_at": "2025-10-13T10:00:00+00:00",
    "updated_at": "2025-10-13T10:00:00+00:00"
  }
}
```

## Testing

The application includes comprehensive unit and feature tests.

### Run All Tests

```bash
docker-compose exec app php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests only
docker-compose exec app php artisan test --testsuite=Unit

# Feature tests only
docker-compose exec app php artisan test --testsuite=Feature

# Specific test file
docker-compose exec app php artisan test --filter TaskServiceTest
```

### Run with Coverage

```bash
docker-compose exec app php artisan test --coverage
```

## Code Quality

### Check Code Style (PSR-12)

```bash
docker-compose exec app ./vendor/bin/phpcs
```

### Auto-fix Code Style

```bash
docker-compose exec app ./vendor/bin/phpcbf
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - User's email (unique)
- `password` - Hashed password
- `role` - User role (owner/team_member)
- `account_id` - Foreign key to parent user (for team members)
- `timestamps`

### Buildings Table
- `id` - Primary key
- `account_id` - Foreign key to users (owner)
- `name` - Building name
- `address` - Building address
- `timestamps`

### Tasks Table
- `id` - Primary key
- `building_id` - Foreign key to buildings
- `title` - Task title
- `description` - Task description
- `assigned_to` - Foreign key to users
- `created_by` - Foreign key to users
- `status` - Task status (enum: open, in_progress, completed, rejected)
- `due_date` - Task due date
- `timestamps`
- Indexes on: `(building_id, status)`, `(assigned_to, created_at)`

### Comments Table
- `id` - Primary key
- `task_id` - Foreign key to tasks
- `user_id` - Foreign key to users
- `content` - Comment text
- `timestamps`
- Index on: `task_id`

## Project Structure

```
.
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/          # API Controllers
│   │   ├── Requests/         # Form Request Validation
│   │   └── Resources/        # API Resources
│   ├── Models/               # Eloquent Models
│   └── Services/             # Business Logic Layer
├── database/
│   ├── factories/            # Model Factories
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Database Seeders
├── routes/
│   └── api.php               # API Routes
├── tests/
│   ├── Feature/              # Feature Tests
│   └── Unit/                 # Unit Tests
├── docker-compose.yml        # Docker Compose Configuration
├── Dockerfile                # Docker Image Configuration
├── phpcs.xml                 # PHP CodeSniffer Configuration
└── README.md                 # This file
```

## Architecture & Design Patterns

### Service Layer Pattern
Business logic is separated into service classes (`TaskService`, `CommentService`) to keep controllers thin and promote reusability.

### Repository Pattern
Models use Eloquent ORM with proper relationships, acting as a data access layer.

### Resource Pattern
API responses use Laravel Resources for consistent data transformation and to hide internal implementation details.

### Dependency Injection
Controllers use constructor injection for services, promoting testability and loose coupling.

### Form Request Validation
Validation logic is separated into dedicated request classes for better organization and reusability.

## Useful Docker Commands

```bash
# Access application container
docker-compose exec app bash

# Access MySQL
docker-compose exec db mysql -u laravel -p

# View logs
docker-compose logs -f app
docker-compose logs -f web
docker-compose logs -f db

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Clear Laravel cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

## Access Points

- **API Base URL**: http://localhost:8000/api
- **Database** (from host): localhost:3307
- **Database** (from containers): db:3306

## Troubleshooting

### Port Already in Use

If you get an error about port 3306 being in use:
- The MySQL port has been mapped to 3307 on the host to avoid conflicts
- Connect from your host machine using port 3307
- From inside Docker containers, use port 3306

### Permission Issues

If you encounter permission issues with Laravel:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues

If migrations fail:
1. Ensure the database container is running: `docker-compose ps`
2. Check the logs: `docker-compose logs db`
3. Verify `.env` settings match `docker-compose.yml`

## Security Considerations

- All passwords are hashed using bcrypt
- API endpoints are protected with Laravel Sanctum authentication
- SQL injection protection through Eloquent ORM
- CSRF protection disabled for API routes (using token authentication instead)
- Input validation on all endpoints

## Future Enhancements

Potential improvements for production use:
- Rate limiting on API endpoints
- Email notifications for task assignments
- File attachments for tasks
- Task priority levels
- Task categories/tags
- Advanced reporting and analytics
- Real-time notifications using WebSockets
- Role-based permissions system

## License

This is a technical assessment project for Proprli.

## Contact

For questions or issues, please contact the development team.

---

**Note**: This is a development environment. For production deployment, additional security hardening, performance optimization, and monitoring should be implemented.
