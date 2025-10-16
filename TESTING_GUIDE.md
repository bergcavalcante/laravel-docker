# Proprli Task Management API - Complete Testing Guide

**Version:** 1.0  
**Date:** October 15, 2025  
**Author:** Proprli Development Team

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Initial Setup](#initial-setup)
3. [Starting the Application](#starting-the-application)
4. [Database Setup](#database-setup)
5. [Importing Postman Collection](#importing-postman-collection)
6. [Testing Workflow](#testing-workflow)
7. [API Endpoints Reference](#api-endpoints-reference)
8. [Troubleshooting](#troubleshooting)
9. [Advanced Testing Scenarios](#advanced-testing-scenarios)

---

## 1. Prerequisites

Before starting, ensure you have the following installed on your system:

### Required Software
- **Docker Desktop** (Version 20.10+)
  - Download: https://www.docker.com/products/docker-desktop
  - Verify installation: `docker --version`
  
- **Docker Compose** (Usually included with Docker Desktop)
  - Verify installation: `docker-compose --version`
  
- **Postman** (Latest version)
  - Download: https://www.postman.com/downloads/
  - Alternative: You can use the web version at https://web.postman.com

### System Requirements
- **RAM:** Minimum 4GB (8GB recommended)
- **Disk Space:** 2GB free space
- **Ports:** Ensure ports 8000, 3306, and 3307 are available
- **Operating System:** Windows 10+, macOS 10.15+, or Linux

---

## 2. Initial Setup

### Step 1: Clone or Navigate to Project Directory

```bash
# If cloning from repository
git clone <your-repository-url>
cd laravel-docker

# Or if you already have the project
cd /path/to/laravel-docker
```

### Step 2: Verify Project Structure

Ensure the following files exist:
```bash
ls -la
```

You should see:
- `docker-compose.yml`
- `Dockerfile`
- `.env` (or `.env.example`)
- `Proprli_Task_Management_API.postman_collection.json`
- `TESTING_GUIDE.md` (this file)

### Step 3: Check Docker Status

```bash
# Check if Docker is running
docker info

# If you get an error, start Docker Desktop application
```

---

## 3. Starting the Application

### Step 1: Stop Any Running Containers

```bash
# Stop and remove any existing containers
docker-compose down

# Remove volumes if you want a completely fresh start
docker-compose down -v
```

### Step 2: Start Docker Containers

```bash
# Start all containers in detached mode
docker-compose up -d
```

**Expected Output:**
```
Creating network "laravel-docker_laravel" with the default driver
Creating laravel_db ... done
Creating laravel_app ... done
Creating laravel_web ... done
```

### Step 3: Verify Containers Are Running

```bash
docker-compose ps
```

**Expected Output:**
```
    Name                  Command              State           Ports
-------------------------------------------------------------------------------
laravel_app    docker-php-entrypoint php-fpm   Up      9000/tcp
laravel_db     docker-entrypoint.sh mysqld     Up      0.0.0.0:3307->3306/tcp
laravel_web    /docker-entrypoint.sh ngin...   Up      0.0.0.0:8000->80/tcp
```

All containers should show **"Up"** status.

### Step 4: Wait for Services to Initialize

```bash
# Wait 10-15 seconds for MySQL to fully start
sleep 15

# Check container logs (optional)
docker-compose logs -f app
# Press Ctrl+C to exit logs view
```

---

## 4. Database Setup

### Step 1: Verify Database Connection

```bash
# Test MySQL connection
docker exec laravel_db mysql -u laravel -plaravel -e "SHOW DATABASES;"
```

**Expected Output:**
```
+--------------------+
| Database           |
+--------------------+
| information_schema |
| laravel            |
+--------------------+
```

### Step 2: Run Database Migrations

```bash
# Run migrations to create all tables
docker exec laravel_app php artisan migrate:fresh
```

**Expected Output:**
```
Dropping all tables .............................................. DONE
Creating migration table ......................................... DONE

Running migrations:
  2014_10_12_000000_create_users_table ............................ DONE
  2014_10_12_100000_create_password_reset_tokens_table ............ DONE
  2019_08_19_000000_create_failed_jobs_table ...................... DONE
  2019_12_14_000001_create_personal_access_tokens_table ........... DONE
  2025_10_14_000231_add_role_and_account_to_users_table ........... DONE
  2025_10_14_000234_create_buildings_table ........................ DONE
  2025_10_14_000235_create_tasks_table ............................ DONE
  2025_10_14_000239_create_task_comments_table .................... DONE
```

### Step 3: (Optional) Seed Test Data

```bash
# Seed the database with sample data
docker exec laravel_app php artisan db:seed
```

This will create:
- 1 owner account
- 7 team members
- 3 buildings
- Multiple tasks with various statuses
- Comments on tasks

### Step 4: Verify Tables Were Created

```bash
# List all tables in the database
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SHOW TABLES;"
```

**Expected Output:**
```
+----------------------------+
| Tables_in_laravel          |
+----------------------------+
| buildings                  |
| failed_jobs                |
| migrations                 |
| password_reset_tokens      |
| personal_access_tokens     |
| task_comments              |
| tasks                      |
| users                      |
+----------------------------+
```

### Step 5: Test Application is Accessible

```bash
# Test the base URL
curl http://localhost:8000

# Or open in browser
open http://localhost:8000
```

---

## 5. Importing Postman Collection

### Step 1: Open Postman

Launch the Postman application on your computer.

### Step 2: Import Collection and Environment

**Import Collection:**
1. Click **"Import"** button (top left corner)
2. Click **"Upload Files"**
3. Navigate to your project directory
4. Select `Proprli_Task_Management_API.postman_collection.json`
5. Click **"Import"**

**Import Environment (Recommended):**
1. Click **"Import"** button again
2. Select `Proprli_Local_Environment.postman_environment.json`
3. Click **"Import"**
4. In the top-right corner, select **"Proprli Local Environment"** from dropdown

**Alternative Method:**
- Drag and drop both JSON files directly into Postman window at once

### Step 3: Verify Collection Import

You should see a new collection named **"Proprli Task Management API"** in the left sidebar with three folders:
- Authentication (5 requests)
- Tasks (7 requests)
- Comments (3 requests)

### Step 4: Import Environment (Recommended)

1. Click **"Import"** button again
2. Select **"Proprli_Local_Environment.postman_environment.json"**
3. Click **"Import"**
4. In the top-right corner, select **"Proprli Local Environment"** from the dropdown

**Alternative:** The collection also has variables built-in, so importing the environment is optional but recommended.

**Why use Environment?**
- Easier to switch between Local/Production
- Variables organized in one place
- Better for team collaboration
- `auth_token` is marked as secret (hidden from view)

### Step 5: Review Environment Variables

With environment selected, click the eye icon (👁️) in top-right to see:

**Current Values:**
- `base_url`: http://localhost:8000/api
- `web_url`: http://localhost:8000
- `auth_token`: (empty - will be auto-saved)
- `user_id`: (empty - will be auto-saved)
- `user_email`: (empty - will be auto-saved)
- `user_name`: (empty - will be auto-saved)
- `building_id`: 1 (default)
- `task_id`: (empty - will be auto-saved)
- `comment_id`: (empty - will be auto-saved)
- `team_member_id`: (empty - will be auto-saved)
- `team_member_email`: (empty - will be auto-saved)

### Step 6: (Optional) Production Environment

For production testing:
1. Import **"Proprli_Production_Environment.postman_environment.json"**
2. Update the `base_url` to your production URL
3. Switch environments using the dropdown in top-right corner

---

## 6. Testing Workflow

Follow this step-by-step workflow to test all API endpoints systematically.

### Phase 1: Authentication Testing

#### Test 1.1: Register Owner Account

1. Open **Authentication** folder
2. Click **"Register User (Owner)"**
3. Review the request body in the **Body** tab
4. Click **"Send"**

**Expected Response (201 Created):**
```json
{
  "user": {
    "id": 1,
    "name": "John Owner",
    "email": "owner@example.com",
    "role": "owner",
    "created_at": "2025-10-15T10:00:00.000000Z",
    "updated_at": "2025-10-15T10:00:00.000000Z"
  },
  "token": "1|abcdef123456...",
  "token_type": "Bearer"
}
```

**✅ What to Verify:**
- Response status is **201 Created**
- `token` is present and saved to `{{auth_token}}`
- `user.id` is saved to `{{user_id}}`
- Check the **Console** (View → Show Postman Console) to see: "Token saved: ..."

**❌ If it Fails:**
- Ensure containers are running: `docker-compose ps`
- Check if port 8000 is accessible: `curl http://localhost:8000`
- View app logs: `docker-compose logs app`

#### Test 1.2: Register Team Member

1. Click **"Register User (Team Member)"**
2. Note that `account_id` uses `{{user_id}}` from previous request
3. Click **"Send"**

**Expected Response (201 Created):**
```json
{
  "user": {
    "id": 2,
    "name": "Jane Team Member",
    "email": "member@example.com",
    "role": "team_member",
    "account_id": 1,
    "created_at": "2025-10-15T10:01:00.000000Z",
    "updated_at": "2025-10-15T10:01:00.000000Z"
  },
  "token": "2|xyz789...",
  "token_type": "Bearer"
}
```

#### Test 1.3: Login

1. Click **"Login"**
2. Verify credentials match the owner account
3. Click **"Send"**

**Expected Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Owner",
    "email": "owner@example.com",
    "role": "owner"
  },
  "token": "3|newtoken123...",
  "token_type": "Bearer"
}
```

**✅ What to Verify:**
- Token is automatically updated in `{{auth_token}}`
- You can now use this token for authenticated requests

#### Test 1.4: Get Current User

1. Click **"Get Current User"**
2. Check the **Authorization** tab - it should use `Bearer {{auth_token}}`
3. Click **"Send"**

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "name": "John Owner",
  "email": "owner@example.com",
  "role": "owner",
  "account_id": null,
  "created_at": "2025-10-15T10:00:00.000000Z",
  "updated_at": "2025-10-15T10:00:00.000000Z"
}
```

#### Test 1.5: Logout

1. Click **"Logout"**
2. Click **"Send"**

**Expected Response (200 OK):**
```json
{
  "message": "Successfully logged out"
}
```

**⚠️ Note:** After logout, you'll need to login again to continue testing other endpoints.

---

### Phase 2: Task Management Testing

Before testing tasks, ensure you're logged in (repeat Test 1.3 if needed).

#### Test 2.1: Create Building (Setup)

**Note:** If you ran database seeding, you already have buildings. You can skip this or create a new one.

1. Click **"Create Building (Setup)"** in the Tasks folder
2. Review the request body
3. Click **"Send"**

**Expected Response (201 Created):**
```json
{
  "id": 1,
  "account_id": 1,
  "name": "Downtown Office Building",
  "address": "123 Main Street, New York, NY 10001",
  "created_at": "2025-10-15T10:05:00.000000Z",
  "updated_at": "2025-10-15T10:05:00.000000Z"
}
```

**✅ What to Verify:**
- `building_id` is automatically saved
- Check console: "Building ID saved: 1"

#### Test 2.2: Create Task

1. Click **"Create Task"**
2. Review the body - notice it uses `{{building_id}}` and `{{user_id}}`
3. Modify fields if desired (title, description, due_date)
4. Click **"Send"**

**Expected Response (201 Created):**
```json
{
  "data": {
    "id": 1,
    "building_id": 1,
    "title": "Fix broken window on 3rd floor",
    "description": "The window in room 305 is cracked...",
    "status": "open",
    "due_date": "2025-10-25",
    "assigned_to": {
      "id": 1,
      "name": "John Owner",
      "email": "owner@example.com",
      "role": "owner"
    },
    "created_by": {
      "id": 1,
      "name": "John Owner",
      "email": "owner@example.com",
      "role": "owner"
    },
    "comments": [],
    "created_at": "2025-10-15T10:10:00+00:00",
    "updated_at": "2025-10-15T10:10:00+00:00"
  }
}
```

**✅ What to Verify:**
- `task_id` is automatically saved
- `status` is "open"
- `assigned_to` and `created_by` show full user objects
- `comments` array is empty initially

#### Test 2.3: List All Building Tasks

1. Click **"List Building Tasks (No Filters)"**
2. Click **"Send"**

**Expected Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "building_id": 1,
      "title": "Fix broken window on 3rd floor",
      "description": "...",
      "status": "open",
      "due_date": "2025-10-25",
      "assigned_to": { ... },
      "created_by": { ... },
      "comments": [],
      "created_at": "2025-10-15T10:10:00+00:00",
      "updated_at": "2025-10-15T10:10:00+00:00"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/buildings/1/tasks?page=1",
    "last": "http://localhost:8000/api/buildings/1/tasks?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost:8000/api/buildings/1/tasks",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

**✅ What to Verify:**
- `data` array contains tasks
- Pagination metadata is present (`links` and `meta`)
- Tasks include related data (comments, users)

#### Test 2.4: Filter Tasks by Status

1. Click **"List Building Tasks (Filter by Status)"**
2. Check the **Params** tab - `status=open`
3. Click **"Send"**

**✅ What to Verify:**
- Only tasks with `status: "open"` are returned
- Try changing the status parameter to `in_progress`, `completed`, or `rejected`

#### Test 2.5: Filter Tasks by Assigned User

1. Click **"List Building Tasks (Filter by Assigned User)"**
2. Check the **Params** tab - `assigned_to={{user_id}}`
3. Click **"Send"**

**✅ What to Verify:**
- Only tasks assigned to the specified user are returned

#### Test 2.6: Multiple Filters

1. Click **"List Building Tasks (Multiple Filters)"**
2. Review the query parameters:
   - `status=open`
   - `assigned_to={{user_id}}`
   - `created_from=2025-10-01`
   - `due_date_to=2025-12-31`
3. Click **"Send"**

**✅ What to Verify:**
- Results match ALL applied filters
- Try modifying date ranges to see different results

---

### Phase 3: Comment Testing

#### Test 3.1: Create First Comment

1. Click **"Create Comment"** in the Comments folder
2. Review the body - it uses `{{task_id}}`
3. Modify the `content` if desired
4. Click **"Send"**

**Expected Response (201 Created):**
```json
{
  "data": {
    "id": 1,
    "task_id": 1,
    "content": "I've started working on this task. Will have it done by end of week.",
    "user": {
      "id": 1,
      "name": "John Owner",
      "email": "owner@example.com",
      "role": "owner"
    },
    "created_at": "2025-10-15T10:15:00+00:00",
    "updated_at": "2025-10-15T10:15:00+00:00"
  }
}
```

**✅ What to Verify:**
- `comment_id` is saved
- `user` object shows who created the comment
- Timestamps are present

#### Test 3.2: Create More Comments

Create 2-3 more comments by:
1. Clicking **"Create Comment"** again
2. Changing the `content` text
3. Clicking **"Send"**

#### Test 3.3: List Task Comments

1. Click **"List Task Comments"**
2. Click **"Send"**

**Expected Response (200 OK):**
```json
{
  "data": [
    {
      "id": 3,
      "task_id": 1,
      "content": "Third comment (most recent)",
      "user": { ... },
      "created_at": "2025-10-15T10:17:00+00:00",
      "updated_at": "2025-10-15T10:17:00+00:00"
    },
    {
      "id": 2,
      "task_id": 1,
      "content": "Second comment",
      "user": { ... },
      "created_at": "2025-10-15T10:16:00+00:00",
      "updated_at": "2025-10-15T10:16:00+00:00"
    },
    {
      "id": 1,
      "task_id": 1,
      "content": "First comment",
      "user": { ... },
      "created_at": "2025-10-15T10:15:00+00:00",
      "updated_at": "2025-10-15T10:15:00+00:00"
    }
  ],
  "links": { ... },
  "meta": {
    "current_page": 1,
    "total": 3,
    "per_page": 15
  }
}
```

**✅ What to Verify:**
- Comments are ordered by most recent first (descending `created_at`)
- Each comment includes full user information
- Pagination metadata is present

#### Test 3.4: Test Pagination

If you have more than 15 comments:
1. Click **"List Task Comments (Page 2)"**
2. Click **"Send"**

**✅ What to Verify:**
- `current_page: 2` in metadata
- Different set of comments returned

---

## 7. API Endpoints Reference

### Quick Reference Table

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/register` | Register new user | No |
| POST | `/api/login` | Login user | No |
| POST | `/api/logout` | Logout user | Yes |
| GET | `/api/user` | Get current user | Yes |
| GET | `/api/buildings/{id}/tasks` | List building tasks | Yes |
| POST | `/api/tasks` | Create task | Yes |
| GET | `/api/tasks/{id}/comments` | List task comments | Yes |
| POST | `/api/comments` | Create comment | Yes |

### Available Filters for Tasks

| Parameter | Type | Values | Example |
|-----------|------|--------|---------|
| `status` | string | open, in_progress, completed, rejected | `?status=open` |
| `assigned_to` | integer | User ID | `?assigned_to=1` |
| `created_from` | date | Y-m-d format | `?created_from=2025-10-01` |
| `created_to` | date | Y-m-d format | `?created_to=2025-10-31` |
| `due_date_from` | date | Y-m-d format | `?due_date_from=2025-10-01` |
| `due_date_to` | date | Y-m-d format | `?due_date_to=2025-12-31` |

### Pagination Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 15 | Items per page (automatic) |

---

## 8. Troubleshooting

### Problem: Containers Won't Start

**Symptoms:**
- `docker-compose up -d` fails
- Containers show "Exited" status

**Solutions:**

```bash
# 1. Check Docker is running
docker info

# 2. Check for port conflicts
lsof -i :8000
lsof -i :3307

# 3. View error logs
docker-compose logs

# 4. Complete reset
docker-compose down -v
docker system prune -a
docker-compose up -d
```

### Problem: Database Connection Failed

**Symptoms:**
- "Connection refused" errors
- Migration failures

**Solutions:**

```bash
# 1. Wait longer for MySQL to initialize
sleep 20

# 2. Check MySQL container logs
docker-compose logs db

# 3. Test connection manually
docker exec laravel_db mysql -u laravel -plaravel -e "SELECT 1;"

# 4. Restart database container
docker-compose restart db
```

### Problem: 401 Unauthorized Errors

**Symptoms:**
- "Unauthenticated" responses
- 401 status code

**Solutions:**

1. **Check Token Variable:**
   - Go to Collection → Variables
   - Verify `auth_token` has a value
   - If empty, re-run Login request

2. **Re-authenticate:**
   ```
   Run: Authentication → Login
   Check: Console shows "Token saved"
   ```

3. **Check Authorization Header:**
   - Open any authenticated request
   - Go to Authorization tab
   - Ensure Type is "Bearer Token"
   - Value should be `{{auth_token}}`

### Problem: 404 Not Found

**Symptoms:**
- Endpoint returns 404
- "Route not found" error

**Solutions:**

```bash
# 1. List all available routes
docker exec laravel_app php artisan route:list

# 2. Check URL is correct
# Base URL should be: http://localhost:8000/api

# 3. Verify container is running
docker-compose ps

# 4. Check Nginx logs
docker-compose logs web
```

### Problem: 422 Validation Errors

**Symptoms:**
- "The given data was invalid"
- 422 status code

**Solutions:**

1. **Check Required Fields:**
   - Review error response for missing fields
   - Example: `"The title field is required"`

2. **Verify Data Types:**
   - Dates must be Y-m-d format: `2025-10-15`
   - IDs must be integers: `1` not `"1"`
   - Status must be valid enum value

3. **Check Foreign Keys:**
   - `building_id` must exist
   - `task_id` must exist
   - `assigned_to` user must exist

### Problem: Empty Responses

**Symptoms:**
- GET requests return empty arrays
- No data despite successful creation

**Solutions:**

```bash
# 1. Check database has data
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT COUNT(*) FROM tasks;"

# 2. Verify filters aren't too restrictive
# Try removing all query parameters

# 3. Check you're using correct building_id
# Run: docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT id, name FROM buildings;"
```

### Problem: Port Already in Use

**Symptoms:**
- "port is already allocated" error
- Can't start containers

**Solutions:**

```bash
# 1. Find what's using the port
lsof -i :8000
lsof -i :3307

# 2. Kill the process (Mac/Linux)
kill -9 <PID>

# 3. Or modify docker-compose.yml ports
# Change "8000:80" to "8080:80" for web service
# Change "3307:3306" to "3308:3306" for db service
```

---

## 9. Advanced Testing Scenarios

### Scenario 1: Multiple Users Workflow

Test collaboration between owner and team members:

```
1. Register Owner (owner@example.com)
2. Register Team Member 1 (member1@example.com)
3. Register Team Member 2 (member2@example.com)

4. Login as Owner
5. Create Building
6. Create Task, assign to Member 1

7. Login as Member 1
8. View assigned tasks
9. Add comment to task

10. Login as Member 2
11. Verify can't see other member's tasks (with filters)
12. Create own task
```

### Scenario 2: Task Lifecycle Testing

Test complete task workflow:

```
1. Create task with status="open"
2. Add initial comment
3. List tasks with status=open (verify appears)

4. Update task to status="in_progress" (manually via database or future endpoint)
5. Add progress comments
6. List tasks with status=in_progress

7. Update task to status="completed"
8. Add final comment
9. Verify filters work correctly
```

### Scenario 3: Pagination Testing

Test pagination with large datasets:

```bash
# 1. Create 30 tasks
docker exec laravel_app php artisan tinker --execute="
  \$building = App\Models\Building::first();
  for(\$i=1; \$i<=30; \$i++) {
    App\Models\Task::factory()->create(['building_id' => \$building->id]);
  }
"

# 2. In Postman:
GET /api/buildings/1/tasks
# Verify: meta.total = 30, per_page = 15, last_page = 2

GET /api/buildings/1/tasks?page=2
# Verify: Shows next 15 tasks
```

### Scenario 4: Date Range Filtering

Test date-based filters:

```
1. Create tasks with different due dates:
   - Task A: due_date = 2025-10-15
   - Task B: due_date = 2025-11-15
   - Task C: due_date = 2025-12-15

2. Test filters:
   ?due_date_from=2025-10-01&due_date_to=2025-10-31
   # Should return only Task A

   ?due_date_from=2025-11-01
   # Should return Tasks B and C

   ?created_from=2025-10-15&created_to=2025-10-15
   # Should return only today's tasks
```

### Scenario 5: Comment Thread Testing

Test comment interactions:

```
1. Create a task
2. Add comment from Owner
3. Add comment from Team Member
4. List comments (verify order is most recent first)
5. Add multiple comments quickly
6. Verify pagination if >15 comments
7. Check user information is included in each comment
```

---

## 10. Useful Commands Reference

### Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down

# Stop, remove containers and volumes (CAUTION: deletes data)
docker-compose down -v

# View container status
docker-compose ps

# View logs
docker-compose logs
docker-compose logs -f app    # Follow logs
docker-compose logs --tail=100 app

# Restart specific container
docker-compose restart app
docker-compose restart db
docker-compose restart web

# Execute commands in container
docker exec laravel_app <command>
docker exec -it laravel_app bash    # Interactive shell
```

### Laravel Artisan Commands

```bash
# Database migrations
docker exec laravel_app php artisan migrate
docker exec laravel_app php artisan migrate:fresh
docker exec laravel_app php artisan migrate:rollback

# Database seeding
docker exec laravel_app php artisan db:seed

# List routes
docker exec laravel_app php artisan route:list

# Clear cache
docker exec laravel_app php artisan cache:clear
docker exec laravel_app php artisan config:clear
docker exec laravel_app php artisan route:clear

# Run tests
docker exec laravel_app php artisan test
docker exec laravel_app php artisan test --filter=TaskControllerTest

# Interactive shell (tinker)
docker exec -it laravel_app php artisan tinker
```

### MySQL Commands

```bash
# Access MySQL CLI
docker exec -it laravel_db mysql -u laravel -plaravel laravel

# Run single query
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT * FROM users;"

# Common queries
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SHOW TABLES;"
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT COUNT(*) FROM tasks;"
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT id, title, status FROM tasks;"
docker exec laravel_db mysql -u laravel -plaravel laravel -e "SELECT id, name, email, role FROM users;"

# Database backup
docker exec laravel_db mysqldump -u laravel -plaravel laravel > backup.sql

# Database restore
docker exec -i laravel_db mysql -u laravel -plaravel laravel < backup.sql
```

### Testing Commands

```bash
# Run all tests
docker exec laravel_app php artisan test

# Run specific test suite
docker exec laravel_app php artisan test --testsuite=Feature
docker exec laravel_app php artisan test --testsuite=Unit

# Run specific test file
docker exec laravel_app php artisan test --filter=TaskControllerTest

# Run with coverage
docker exec laravel_app php artisan test --coverage

# Code style check
docker exec laravel_app ./vendor/bin/phpcs

# Auto-fix code style
docker exec laravel_app ./vendor/bin/phpcbf
```

---

## 11. Quick Start Checklist

Use this checklist for your first time setup:

### Pre-Testing Checklist

- [ ] Docker Desktop is installed and running
- [ ] Postman is installed
- [ ] Project files are in place
- [ ] Ports 8000, 3306, 3307 are available

### Setup Checklist

- [ ] Navigated to project directory
- [ ] Ran `docker-compose down` (cleanup)
- [ ] Ran `docker-compose up -d` (start containers)
- [ ] Waited 15 seconds for services to initialize
- [ ] Ran `docker exec laravel_app php artisan migrate:fresh`
- [ ] Verified `http://localhost:8000` is accessible
- [ ] Imported Postman collection
- [ ] Reviewed collection variables

### Testing Checklist

- [ ] Test 1: Register Owner → Success (201)
- [ ] Test 2: Login → Success (200), token saved
- [ ] Test 3: Get Current User → Success (200)
- [ ] Test 4: Create Task → Success (201), task_id saved
- [ ] Test 5: List Tasks → Success (200), shows created task
- [ ] Test 6: Create Comment → Success (201)
- [ ] Test 7: List Comments → Success (200), shows comment
- [ ] Test 8: Try filters → Success (200), results filtered
- [ ] Test 9: Logout → Success (200)

---

## 12. Support and Resources

### Documentation

- **Laravel Documentation:** https://laravel.com/docs/10.x
- **Docker Documentation:** https://docs.docker.com
- **Postman Documentation:** https://learning.postman.com

### Project Files

- **README.md:** Complete project overview
- **TESTING_GUIDE.md:** This file
- **Postman Collection:** `Proprli_Task_Management_API.postman_collection.json`

### Logs Location

```bash
# Application logs
docker-compose logs app

# Web server logs
docker-compose logs web

# Database logs
docker-compose logs db

# Laravel application logs (inside container)
docker exec laravel_app cat storage/logs/laravel.log
```

---

## 13. Appendix: Sample Data

### Sample Building

```json
{
  "name": "Sunset Plaza",
  "address": "456 Beach Boulevard, Los Angeles, CA 90210"
}
```

### Sample Task

```json
{
  "building_id": 1,
  "title": "Replace air conditioning unit",
  "description": "The AC unit on the 5th floor is making strange noises and needs immediate attention",
  "assigned_to": 2,
  "status": "open",
  "due_date": "2025-11-01"
}
```

### Sample Comment

```json
{
  "task_id": 1,
  "content": "I've inspected the unit. Will need to order parts. ETA 3 business days."
}
```

---

## Conclusion

This guide provides comprehensive instructions for setting up, running, and testing the Proprli Task Management API. Follow the steps in order for the best results.

For additional help or to report issues, contact the development team.

**Happy Testing! 🚀**

---

**Document Version:** 1.0  
**Last Updated:** October 15, 2025  
**Next Review:** November 15, 2025

