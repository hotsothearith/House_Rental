# 🏠 House Rental API (Laravel Backend)

This is the **Laravel-based RESTful API** backend for the House Rental application. It provides endpoints for managing house listings, users, bookings, and more. The API supports full CRUD operations and is designed to work with a separate frontend application.

---

## 🚀 Features

- 🔐 User authentication (login, registration)
- 🏠 CRUD for houses (create, view, update, delete)
- 📦 Image upload for house listings
- 📅 Booking system for renters
- 🔄 JSON-based API responses
- 🛡️ Secured routes using Laravel Sanctum or Passport (if implemented)
- 🌍 CORS enabled for frontend communication

---

## 📦 Tech Stack

- **Laravel 10+**
- **MySQL / MariaDB**
- **Laravel Sanctum** 
- **Laravel File Storage** – for image upload
- **CORS** – for cross-origin frontend access

---

## ⚙️ Installation

1. Clone the repo:
```bash
git clone https://github.com/hotsothearith/House_Rental.git
cd House_Rental
```
2. Install dependencies:
```bash
composer install
```
3. Create .env file:
```bash
cp .env.example .env
   ```
4. Set environment variables (.env):
```bash
DB_DATABASE=your_db_name
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://127.0.0.1:8000
```
5. Generate app key:
```bash
php artisan key:generate
```
6. Run migrations:
```bash
php artisan migrate
```
7. php artisan serve
```bash
php artisan serve
```
🧪 Testing the API
You can test endpoints using:

Postman

Thunder Client (VS Code)

cURL

Frontend app (linked below)
   
