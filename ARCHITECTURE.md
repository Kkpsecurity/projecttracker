# KKP Security Project Tracker - System Architecture

## 🏗️ System Components Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    KKP Security Project Tracker             │
│                        Laravel + AdminLTE                   │
└─────────────────────────────────────────────────────────────┘

┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│   Admin Center  │  │   ProTrack      │  │   Client Portal │
│                 │  │   (Planned)     │  │   (Planned)     │
├─────────────────┤  ├─────────────────┤  ├─────────────────┤
│ ✅ User Mgmt    │  │ ⏳ Projects     │  │ ⏳ Project View │
│ ⚠️ Settings     │  │ ⏳ Time Track   │  │ ⏳ File Access  │
│ ✅ Logs         │  │ ⏳ Clients      │  │ ⏳ Status       │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

## 🔐 Authentication & Authorization Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Login     │───▶│   Auth      │───▶│   Admin     │
│   /admin    │    │ Middleware  │    │   Routes    │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Redirect   │    │  Check      │    │  Admin      │
│  to Login   │    │  is_admin   │    │  Dashboard  │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 📊 Database Schema Relationships

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     users       │    │  site_settings  │    │   projects      │
├─────────────────┤    ├─────────────────┤    │   (planned)     │
│ • id            │    │ • id            │    ├─────────────────┤
│ • name          │    │ • key           │    │ • id            │
│ • email         │    │ • value         │    │ • name          │
│ • password      │    │ • type          │    │ • client_id     │
│ • is_admin ✅   │    │ • group         │    │ • status        │
│ • is_active ✅  │    │ • created_at    │    │ • created_at    │
│ • last_login ✅ │    │ • updated_at    │    │ • updated_at    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                                              │
        └──────────────────────┐      ┌────────────────┘
                               │      │
                               ▼      ▼
                      ┌─────────────────┐
                      │     clients     │
                      │   (planned)     │
                      ├─────────────────┤
                      │ • id            │
                      │ • name          │
                      │ • email         │
                      │ • contact_info  │
                      └─────────────────┘
```

## 🛣️ Route Architecture

```
/admin (AdminLTE Dashboard)
│
├── /login (public)
├── /logout (public)
│
├── / (authenticated admin routes)
│   ├── /dashboard (admin.dashboard)
│   │
│   ├── /users (admin.users.*)
│   │   ├── GET    /           → index (DataTables)
│   │   ├── GET    /data       → getData (AJAX)
│   │   ├── GET    /create     → create form
│   │   ├── POST   /           → store
│   │   ├── GET    /{user}     → show
│   │   ├── GET    /{user}/edit → edit form
│   │   ├── PUT    /{user}     → update
│   │   ├── DELETE /{user}     → destroy
│   │   └── PATCH  /{user}/... → admin actions
│   │
│   ├── /settings (admin.settings.*) ⚠️ 500 ERROR
│   │   ├── GET    /           → index (settings form)
│   │   ├── PUT    /           → update
│   │   ├── POST   /reset      → reset defaults
│   │   └── GET    /toggle-maintenance
│   │
│   ├── /logs (admin.logs.*)
│   │   └── GET    /           → index (activity logs)
│   │
│   └── /projects (admin.projects.*) ⏳ PLANNED
│       ├── GET    /           → index
│       ├── GET    /create     → create form
│       ├── POST   /           → store
│       └── ...
│
└── /api (AJAX endpoints)
    ├── /users/search
    └── /projects/search (planned)
```

## 🎨 Frontend Architecture (AdminLTE)

```
┌─────────────────────────────────────────────────────────────┐
│                     AdminLTE Layout                        │
├─────────────────────────────────────────────────────────────┤
│  Header: Brand, Search, Notifications, User Menu           │
├─────────────────┬───────────────────────────────────────────┤
│   Sidebar       │            Main Content                   │
│                 │                                           │
│ • Dashboard     │  ┌─────────────────────────────────────┐  │
│ • Admin Center  │  │        Content Area                 │  │
│   ├─ Users ✅   │  │                                     │  │
│   ├─ Settings⚠️ │  │  • Breadcrumbs                      │  │
│   └─ Logs ✅    │  │  • Flash Messages                   │  │
│ • ProTrack ⏳   │  │  • Forms/Tables/Charts              │  │
│ • Analytics ⏳  │  │  • Action Buttons                   │  │
│ • Account       │  │                                     │  │
│                 │  └─────────────────────────────────────┘  │
├─────────────────┴───────────────────────────────────────────┤
│  Footer: Copyright, Version, Links                         │
└─────────────────────────────────────────────────────────────┘
```

## 🔧 Technology Stack Detail

### Backend
- **Framework:** Laravel 10.x
- **Authentication:** Laravel Breeze/Auth
- **Database:** MySQL/SQLite
- **Cache:** File/Redis (configurable)
- **Storage:** Local/S3 (configurable)

### Frontend
- **Theme:** AdminLTE 3.2
- **CSS Framework:** Bootstrap 4
- **Icons:** Font Awesome 5
- **DataTables:** Yajra DataTables
- **JavaScript:** jQuery, Bootstrap JS

### Development Tools
- **Version Control:** Git
- **Documentation:** AI-powered error tracking
- **Testing:** PHPUnit (planned)
- **Deployment:** Laravel Forge/Docker (planned)

## 📈 Performance Considerations

### Database Optimization
- Indexed foreign keys
- Cached settings (SiteSettings singleton)
- Paginated results (DataTables)

### Frontend Optimization
- AdminLTE CDN assets
- Lazy loading for large datasets
- AJAX-powered interfaces

### Security Features
- CSRF protection
- SQL injection prevention
- XSS protection
- Admin-only routes middleware

## 🔄 Data Flow Examples

### User Management Flow
```
User clicks "Edit User" 
    ↓
GET /admin/users/{id}/edit 
    ↓
UserController@edit 
    ↓
Load user from database 
    ↓
Return edit.blade.php with user data 
    ↓
User submits form 
    ↓
PUT /admin/users/{id} 
    ↓
UserController@update 
    ↓
Validate & save to database 
    ↓
Redirect with success message
```

### Site Settings Flow (Currently Broken)
```
User clicks "System Settings" 
    ↓
GET /admin/settings 
    ↓
❌ 500 SERVER ERROR
    ↓
⚠️ DEBUGGING REQUIRED
```

---

**Created:** June 28, 2025  
**Status:** System architecture documentation  
**Next Update:** After 500 error resolution
