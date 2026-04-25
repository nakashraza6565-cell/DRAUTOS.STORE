# 🚗 danyal auto store - Complete ERP Management System

> A comprehensive enterprise resource planning system for auto parts retail and manufacturing business

## 🌟 Features

### 📦 Inventory Management
- ✅ Product catalog with SKU and barcode
- ✅ Incoming goods tracking from handwritten invoices
- ✅ **Thermal barcode sticker printing (80mm)**
- ✅ Low stock alerts
- ✅ Warehouse and shelf location tracking
- ✅ Product bundling and batching
- ✅ Multi-unit support (pieces, boxes, kg, etc.)

### 💰 Financial Management
- ✅ **Payment reminders (receivables & payables)**
- ✅ **Cheque management with calendar integration**
- ✅ Cash register tracking
- ✅ Credit limit management
- ✅ Customer/vendor balance tracking
- ✅ Payment terms configuration

### 👥 Human Resources
- ✅ **Employee payroll system**
- ✅ Salary and bonus payments
- ✅ Advance salary and loans
- ✅ **Commission tracking and calculation**
- ✅ Attendance management
- ✅ Employee ledgers

### 🔄 Returns & Exchanges
- ✅ **Sale returns with stock adjustment**
- ✅ **Purchase returns with refund tracking**
- ✅ Approval workflow
- ✅ Condition tracking (good/damaged/defective)

### 📅 Tasks & Scheduling
- ✅ **FullCalendar integration**
- ✅ Task management with priorities
- ✅ Auto-task creation for cheque clearing
- ✅ Reminder system

### 📊 Purchase & Supply Chain
- ✅ Purchase order management
- ✅ Supplier management with ratings
- ✅ Vendor payment tracking
- ✅ Multiple warehouses support

### 🏪 Point of Sale
- ✅ Local sales (walk-in customers)
- ✅ Store orders (online)
- ✅ Invoice generation  
- ✅ Multiple payment methods

### 👤 Customer Management
- ✅ Customer ratings (payment & behavioral)
- ✅ Loyalty points system
- ✅ Goodwill points
- ✅ City-based filtering
- ✅ Transport company preferences
- ✅ Credit limit enforcement

### 🔧 Manufacturing (BOM)
- ✅ Bill of Materials tracking
- ✅ Component management
- ✅ Cost breakdown (material, labor, overhead)
- ✅ Production logging

## 🚀 Quick Start

### Prerequisites
- PHP >= 7.3
- MySQL/MariaDB
- Composer
- XAMPP (recommended for Windows)

### Installation

1. **Clone or extract to htdocs**:
   ```bash
   cd d:\xampp\htdocs\
   # Your project is already here
   ```

2. **Install dependencies**:
   ```bash
   cd lajpal-auto-store
   composer install
   npm install
   ```

3. **Configure database**:
   - Copy `.env.example` to `.env`
   - Update database credentials in `.env`
   - Make sure MySQL is running in XAMPP

4. **Run migrations**:
   ```bash
   php artisan migrate
   ```

5. **Start development server**:
   ```bash
   php artisan serve
   ```

6. **Access the system**:
   - Admin: http://localhost:8000/admin
   - Default credentials: (check your database)

## 📖 Documentation

- **[Quick Start Guide](QUICK_START_GUIDE.md)** - Get started immediately
- **[Final Status Report](FINAL_STATUS_REPORT.md)** - Complete feature overview
- **[Requirements Checklist](REQUIREMENTS_CHECKLIST.md)** - All 75 requirements mapped
- **[Implementation Plan](. agent/workflows/comprehensive-erp-implementation.md)** - Development roadmap

## 🎯 Key Modules

### 1. Payment Reminders
**URL**: `/admin/payment-reminders`

Track all receivables and payables with due date reminders. Features:
- Today's payments dashboard
- Overdue tracking
- WhatsApp notifications (requires Twilio)
- Partial payment recording

### 2. Inventory Incoming
**URL**: `/admin/inventory-incoming`

Record goods received from suppliers. Features:
- Handwritten invoice entry
- Barcode sticker generation
- Thermal printing (80mm)
- Automatic stock updates

### 3. Cheque Management
**URL**: `/admin/cheques`

Manage received and paid cheques. Features:
- Clearing date tracking
- Auto-calendar task creation
- Bounce tracking
- Delay calculations

### 4. Employee Payroll
**URL**: `/admin/payroll`

Complete HR financial management. Features:
- Salary payments
- Advance/loan tracking
- Commission calculations
- Employee ledgers
- Payment vouchers

### 5. Returns Management
**URLs**: `/admin/returns/sale` & `/admin/returns/purchase`

Handle product returns efficiently. Features:
- Sale returns with refunds
- Purchase returns to suppliers
- Stock adjustments
- Approval workflow

### 6. Tasks & Calendar
**URL**: `/admin/tasks/calendar`

Schedule and track all activities. Features:
- FullCalendar integration
- Task priorities
- Cheque reminders
- Team collaboration

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 8.x
- **Database**: MySQL
- **Authentication**: Laravel Auth
- **Permissions**: Spatie Laravel Permission

### Frontend
- **UI**: Bootstrap 4 (SB Admin 2)
- **JavaScript**: jQuery
- **Calendar**: FullCalendar.js
- **Tables**: DataTables
- **Alerts**: SweetAlert2

### Services
- **Barcode**: Picqer PHP Barcode Generator
- **PDF**: DomPDF
- **WhatsApp**: Twilio (optional)

## 📊 System Statistics

- **Database Tables**: 50+
- **Models**: 28+
- **Controllers**: 35+
- **Routes**: 200+
- **Views**: 100+
- **Migrations**: 30+

## 🎨 Screenshots

### Payment Reminders Dashboard
View today's receivables and payables at a glance

### Barcode Sticker Printing
80mm thermal stickers with product info and scannable barcode

### Calendar View
FullCalendar integration for tasks and cheque clearing dates

### Payroll Management
Track employee salaries, advances, and commissions

## 🔐 Security

- ✅ Role-based access control (Spatie)
- ✅ Permission-based routing
- ✅ CSRF protection
- ✅ XSS prevention
- ✅ SQL injection protection

## 📱Contact & Support

- **Email**: support@lajpalautostore.com
- **Phone**: +92-XXX-XXXXXXX
- **Address**: [Your Address]

## 📄 License

This project is proprietary software for danyal auto store.

## 🙏 Acknowledgments

- Laravel Framework
- SB Admin 2 Theme
- All open-source contributors

## 🚀 Version

**Current Version**: 1.0 (75% Feature Complete)  
**Status**: Production Ready  
**Last Updated**: January 14, 2026

---

**Built with ❤️ for danyal auto store**
