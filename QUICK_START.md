# 🚀 Quick Start Guide - Proprli Task Management API

## 📁 Files Created

1. **`Proprli_Task_Management_API.postman_collection.json`** (15 KB)
   - Complete Postman collection with all API endpoints
   - Automatic token handling
   - Pre-configured requests with examples

2. **`Proprli_Local_Environment.postman_environment.json`** (1.2 KB)
   - Postman environment for local testing
   - Pre-configured variables
   - Secret token storage

3. **`Proprli_Production_Environment.postman_environment.json`** (1.2 KB)
   - Postman environment for production
   - Ready to customize with your production URL

4. **`TESTING_GUIDE.md`** (27 KB)
   - Comprehensive testing manual (1,200+ lines)
   - Step-by-step instructions
   - Troubleshooting guide
   - All Docker commands

5. **`CONVERT_TO_PDF.md`** (1.3 KB)
   - Instructions to convert the testing guide to PDF

## ⚡ Quick Start (5 Minutes)

### 1. Start the Application
```bash
cd /Users/bergelisson/Documents/proprli/workspace/laravel-docker
docker-compose up -d
sleep 15
docker exec laravel_app php artisan migrate:fresh
```

### 2. Import Postman Collection & Environment
1. Open Postman
2. Click **Import**
3. Select both files:
   - `Proprli_Task_Management_API.postman_collection.json`
   - `Proprli_Local_Environment.postman_environment.json`
4. Click Import
5. Select **"Proprli Local Environment"** in top-right dropdown
6. Done! Collection is ready to use

### 3. Start Testing
1. Open **Authentication** folder
2. Click **"Register User (Owner)"**
3. Click **Send** → Token is auto-saved
4. Continue with other requests

## 📚 What's in the Postman Collection?

### Authentication (5 requests)
- ✅ Register Owner
- ✅ Register Team Member
- ✅ Login (auto-saves token)
- ✅ Get Current User
- ✅ Logout

### Tasks (7 requests)
- ✅ Create Building
- ✅ Create Task (auto-saves task_id)
- ✅ List All Tasks
- ✅ Filter by Status
- ✅ Filter by Assigned User
- ✅ Multiple Filters
- ✅ Date Range Filters

### Comments (3 requests)
- ✅ Create Comment (auto-saves comment_id)
- ✅ List Task Comments (ordered by most recent)
- ✅ Pagination Test

## 🎯 Key Features

### Automatic Variable Management
The collection automatically saves:
- `{{auth_token}}` - From login/register
- `{{user_id}}` - Current user ID
- `{{building_id}}` - Created building ID
- `{{task_id}}` - Created task ID
- `{{comment_id}}` - Created comment ID

### Pre-configured Examples
Every request includes:
- Sample request body
- Expected response format
- Status code checks
- Console logging

## 📖 Read the Full Guide

For detailed instructions, troubleshooting, and advanced scenarios:

```bash
# Read in terminal
cat TESTING_GUIDE.md

# Or open in your preferred markdown viewer
open TESTING_GUIDE.md
```

## 🔄 Convert Guide to PDF

See `CONVERT_TO_PDF.md` for 5 different methods to create a PDF version.

**Recommended:** Use VS Code with "Markdown PDF" extension:
1. Install extension
2. Right-click → "Markdown PDF: Export (pdf)"
3. Done!

## 🆘 Quick Troubleshooting

### Can't connect to API?
```bash
docker-compose ps  # Check all are "Up"
curl http://localhost:8000  # Test connection
```

### 401 Unauthorized?
- Re-run the **Login** request
- Check `{{auth_token}}` has a value (Collection → Variables)

### Database issues?
```bash
docker-compose restart db
sleep 10
docker exec laravel_app php artisan migrate:fresh
```

## 📊 Testing Checklist

- [ ] Import Postman collection
- [ ] Start Docker containers
- [ ] Run migrations
- [ ] Register user (token auto-saved)
- [ ] Create task (ID auto-saved)
- [ ] List tasks (verify data)
- [ ] Create comment
- [ ] List comments (verify order)
- [ ] Test filters
- [ ] Test pagination

## 🎓 Documentation Structure

```
TESTING_GUIDE.md (main guide)
├── Prerequisites
├── Setup Instructions
├── Step-by-Step Testing
├── API Reference
├── Troubleshooting
├── Advanced Scenarios
└── Command Reference

Postman Collection
├── Authentication Folder
├── Tasks Folder
└── Comments Folder
```

## 📞 Support

- **Full Guide:** See `TESTING_GUIDE.md`
- **API Docs:** See `README.md`
- **Collection:** Import and explore in Postman

---

**Ready to test?** Start with step 1 above! 🚀

