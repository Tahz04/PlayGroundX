# ⚽ PlayGroundX

### Real-Time Sports Field Booking and Management System

PlayGroundX là hệ thống web cho phép người dùng **tìm kiếm, đặt sân thể thao và quản lý lịch đặt sân theo thời gian thực**.
Hệ thống hỗ trợ **khách hàng, chủ sân và quản trị viên** quản lý hoạt động đặt sân một cách hiệu quả.

---

# 📌 Mục tiêu hệ thống

* Tìm kiếm sân thể thao
* Đặt sân theo khung giờ
* Quản lý lịch đặt sân
* Quản lý người dùng
* Cập nhật trạng thái sân theo thời gian thực
* Hỗ trợ chủ sân quản lý doanh thu và hoạt động kinh doanh

---

# 👥 Đối tượng sử dụng

## 1️⃣ Khách hàng (Customer)

Có thể:

* Đăng ký tài khoản
* Đăng nhập
* Xem danh sách sân
* Đặt sân
* Xem lịch đặt sân
* Đánh giá sân

---

## 2️⃣ Chủ sân (Owner)

Có thể:

* Quản lý sân
* Thêm / sửa / xóa sân
* Upload ảnh sân
* Xem đơn đặt sân
* Quản lý lịch sân
* Xem doanh thu

---

## 3️⃣ Quản trị viên (Admin)

Có thể:

* Quản lý người dùng
* Quản lý sân
* Quản lý booking
* Xem thống kê hệ thống

---

# ⚙️ Công nghệ sử dụng

## Backend

* Laravel Framework
* PHP
* MySQL

## Frontend

* Blade Template
* JavaScript
* Bootstrap / TailwindCSS

## Realtime

* Laravel Broadcasting
* Pusher / WebSocket

## API

* REST API
* JSON

---

# 🚀 Cài đặt project

## 1️⃣ Clone project

```
git clone https://github.com/Tahz04/playgroundx.git
cd playgroundx
```

---

## 2️⃣ Cài dependencies

```
composer install
npm install
```

---

## 3️⃣ Tạo file môi trường

```
cp .env.example .env
```

Sau đó cấu hình database trong `.env`

```
DB_DATABASE=playgroundx
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4️⃣ Generate key

```
php artisan key:generate
```

---

## 5️⃣ Chạy migration

```
php artisan migrate
```

---

## 6️⃣ Chạy server

```
php artisan serve
```

Truy cập:

```
http://127.0.0.1:8000
```

---

# 📡 REST API Examples

### Lấy danh sách sân

```
GET /api/arenas
```

### Lấy danh sách booking

```
GET /api/bookings
```

### Đặt sân

```
POST /api/bookings
```

---

# 🌟 Chức năng nâng cao

* Tìm kiếm sân
* Sắp xếp theo giá
* Upload ảnh sân
* Realtime booking
* Dashboard thống kê

---

# 👨‍💻 Nhóm phát triển

THÀNH
DUY
HẰNG

---