# 📘 Hotel Management RESTful API - Kiến Trúc & Hoạt Động

---

## 📋 MỤC LỤC

1. [Tổng Quan](#1-tổng-quan)
2. [Kiến Trúc Hệ Thống](#2-kiến-trúc-hệ-thống)
3. [Luồng Xử Lý Request](#3-luồng-xử-lý-request)
4. [Cấu Trúc Thư Mục](#4-cấu-trúc-thư-mục)
5. [Chi Tiết Từng Thành Phần](#5-chi-tiết-từng-thành-phần)
6. [Danh Sách API Endpoints](#6-danh-sách-api-endpoints)
7. [Chuẩn RESTful Áp Dụng](#7-chuẩn-restful-áp-dụng)
8. [Authentication & Authorization](#8-authentication--authorization)
9. [Database Schema](#9-database-schema)
10. [Ví Dụ Request/Response](#10-ví-dụ-requestresponse)
11. [Cách Test & Debug](#11-cách-test--debug)

---

## 1. TỔNG QUAN

Hệ thống quản lý khách sạn được nâng cấp từ **MVC truyền thống** lên **RESTful API** theo kiến trúc của [afgprogrammer/PHP-MVC-REST-API](https://github.com/afgprogrammer/PHP-MVC-REST-API).

### Đặc điểm chính:

| Tính năng | Mô tả |
|-----------|-------|
| **Framework tự viết** | Không dùng Laravel, Slim, hay bất kỳ framework ngoài nào |
| **Đúng chuẩn RESTful** | URL theo resource, HTTP methods chuẩn, JSON response |
| **JWT Authentication** | Bảo mật bằng token (Bearer token trong header) |
| **Song song MVC + REST** | Web app cũ vẫn hoạt động, API chạy độc lập |
| **CORS Support** | Cho phép cross-origin requests từ frontend khác |
| **HTTP Status Codes** | 200, 201, 400, 401, 404, 409, 500... |
| **PDO Database** | Prepared statements, chống SQL injection |

---

## 2. KIẾN TRÚC HỆ THỐNG

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT (Postman / Browser / Mobile)       │
│              GET /api/health, POST /api/auth/login, ...          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Apache Web Server                         │
│  .htaccess → RewriteRule → index.php (nếu file không tồn tại)    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        index.php (Entry Point)                   │
│                                                                  │
│  if (URI chứa '/api') → REST API MODE                           │
│  else → MVC WEB APP MODE (code cũ - bridge.php → App.php)        │
└────────────────────────────┬────────────────────────────────────┘
                             │
              ┌──────────────┴──────────────┐
              │ REST API MODE               │
              │                             │
              ▼                             │
┌─────────────────────────┐                 │
│  config.php              │                 │
│  → Định nghĩa paths      │                 │
│  → Cấu hình database     │                 │
└────────────┬─────────────┘                 │
             │                               │
             ▼                               │
┌─────────────────────────┐                 │
│  Startup.php             │                 │
│  → spl_autoload_register │                 │
│  → Tự động load class    │                 │
└────────────┬─────────────┘                 │
             │                               │
             ▼                               │
┌─────────────────────────┐                 │
│  Http\Request            │                 │
│  → Parse method, URL     │                 │
│  → Parse JSON body       │                 │
│  → Parse headers         │                 │
└────────────┬─────────────┘                 │
             │                               │
             ▼                               │
┌─────────────────────────┐                 │
│  Http\Response           │                 │
│  → Set CORS headers      │                 │
│  → Set Content-Type      │                 │
│  → Set status code       │                 │
│  → Render JSON output    │                 │
└────────────┬─────────────┘                 │
             │                               │
             ▼                               │
┌─────────────────────────┐                 │
│  Router\Router           │                 │
│  → Match URL vs patterns │                 │
│  → Call Controller       │                 │
└────────────┬─────────────┘                 │
             │                               │
             ▼                               │
┌─────────────────────────────────────────────────────────────────┐
│                   Application (Business Logic)                  │
│                                                                 │
│  Controllers/                      Models/                      │
│  ├── auth.php                     ├── auth.php                  │
│  ├── services.php                 ├── services.php              │
│  ├── rooms.php                    ├── rooms.php                 │
│  ├── roomtypes.php                ├── roomtypes.php             │
│  ├── bookings.php                 ├── bookings.php              │
│  ├── guests.php                   ├── guests.php                │
│  ├── payments.php                 ├── payments.php              │
│  ├── serviceorders.php            └── serviceorders.php         │
│  └── reports.php                                                │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        MySQL Database (PDO)                      │
│                                                                  │
│  hotelservice_services, hotelservice_servicesused                │
│  bookings_booking, rooms_room, rooms_roomtype, rooms_roombooked  │
│  hotels_guests, payments_payment                                 │
│  authentication_admin, authentication_login                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. LUỒNG XỬ LÝ REQUEST

### Ví dụ 1: `GET /api/health`

```
Bước 1: Client gửi request
   GET http://localhost/Hotel_Management_Website/api/health

Bước 2: Apache xử lý
   - Kiểm tra file /api/health có tồn tại? → KHÔNG
   - .htaccess rewrite → index.php?$1

Bước 3: index.php phân loại
   - URI = "/Hotel_Management_Website/api/health"
   - Kiểm tra: URI chứa '/api'? → CÓ
   - Vào REST API MODE

Bước 4: Load config.php
   - Định nghĩa: SYSTEM, CONTROLLERS, MODELS
   - Cấu hình database: host, user, pass, db name, port

Bước 5: Load Startup.php
   - Đăng ký autoloader: spl_autoload_register('autoload')
   - Hàm autoload(): Tự động require file class khi cần
   - Load helper functions: clean(), cleanUrl()

Bước 6: Tạo Http\Request object
   - $this->method = 'GET'
   - $this->url = '/Hotel_Management_Website/api/health'
   - Parse headers, cookies, files

Bước 7: Tạo Http\Response object
   - Set header: Access-Control-Allow-Origin: *
   - Set header: Content-Type: application/json; charset=UTF-8

Bước 8: Tạo Router\Router object
   - Clean URL: loại bỏ '/Hotel_Management_Website' → '/api/health'
   - Method: GET

Bước 9: Load Router/Router.php (định nghĩa routes)
   - $router->get('/api/health', function() { ... })
   - Đăng ký 50+ routes khác

Bước 10: Router->run()
   - Lọc routes theo method (GET)
   - So khớp URL với từng pattern bằng regex
   - Khớp: '/api/health' → callback function

Bước 11: Thực thi callback
   $response->setContent([
       'success' => true,
       'message' => 'OK',
       'data' => ['time' => date('c')]
   ]);

Bước 12: Response->render()
   - Set HTTP status code: 200
   - Set headers: Content-Type, CORS
   - Echo JSON: {"success":true,"message":"OK","data":{"time":"..."}}
   - exit

Bước 13: Client nhận JSON response ✅
```

### Ví dụ 2: `POST /api/auth/login`

```
Bước 1: Client gửi request
   POST http://localhost/.../api/auth/login
   Body: {"username":"admin","password":"123456"}

Bước 2-9: (Giống ví dụ 1) → Router khớp route: 'auth@login'

Bước 10: Router->runController('auth@login')
   - Tìm file: Application/Controllers/auth.php
   - require_once file
   - Tạo object: $controller = new ControllersAuth()
   - Gọi method: $controller->login()

Bước 11: ControllersAuth->login()
   - $data = $this->request->input() → ['username'=>'admin','password'=>'123456']
   - Validate: username và password phải có
   - $user = $this->model('auth')->authenticate('admin', '123456')

Bước 12: ModelsAuth->authenticate()
   - Query 1: SELECT * FROM authentication_admin WHERE TenDangNhap='admin' AND MatKhau='123456'
   - Nếu có → return ['id'=>'AD001','username'=>'admin','role'=>'admin',...]
   - Nếu không → Query 2: authentication_login
   - Nếu không → Query 3: hotels_guests (theo SĐT)
   - Nếu không → return null

Bước 13: ControllersAuth->login()
   - $token = $this->model('auth')->generateToken($user)
     → JWT: header.payload.signature
   - $this->send(200, [
       'success' => true,
       'message' => 'Login successful',
       'data' => ['token' => $token, 'user' => $user]
     ])

Bước 14: Response->render() → JSON trả về client ✅
```

### Ví dụ 3: `POST /api/bookings` (Tạo booking)

```
Bước 1: Client gửi request
   POST /api/bookings
   Body: {
     "MaKhachHang": "KH001",
     "NgayNhanPhong": "2026-05-01",
     "NgayTraPhong": "2026-05-03",
     "MaLoaiPhong": "LP001"
   }

Bước 2-10: Router → ControllersBookings->store()

Bước 11: ControllersBookings->store()
   - Validate: MaKhachHang, NgayNhanPhong, NgayTraPhong phải có
   - $this->model('bookings')->create($data)

Bước 12: ModelsBookings->create()
   - Tạo ID: MaDatPhong = 'DP' + timestamp + random
   - Tính số ngày: (NgayTra - NgayNhan) / 86400
   - Query giá phòng: SELECT GiaPhong FROM rooms_roomtype WHERE MaLoaiPhong='LP001'
   - Tính tổng tiền: số ngày × giá phòng
   - INSERT INTO bookings_booking (...) VALUES (...)
   - Return $this->getById($id)

Bước 13: ControllersBookings->store()
   - $this->send(201, [
       'success' => true,
       'message' => 'Booking created',
       'data' => $booking
     ])

Bước 14: Client nhận JSON với status 201 Created ✅
```

---

## 4. CẤU TRÚC THƯ MỤC

```
Hotel_Management_Website/                    ← Thư mục gốc (làm việc)
│                                            ← Copy sang D:\xampp\htdocs\... để chạy
├── index.php                                ← 🟢 Entry point (phân loại API vs MVC)
├── config.php                               ← 🟢 Cấu hình database, đường dẫn
├── .htaccess                                ← 🟢 Apache URL rewriting
│
├── System/                                  ← 🔵 FRAMEWORK CORE (tự viết từ đầu)
│   ├── Startup.php                          ← Autoloader (spl_autoload_register)
│   │
│   ├── Http/
│   │   ├── Request.php                      ← Xử lý HTTP request (method, URL, body)
│   │   └── Response.php                    ← Xử lý JSON response + CORS + status
│   │
│   ├── Router/
│   │   ├── Router.php                      ← 🟢 Router: khớp URL → gọi Controller
│   │   └── Route.php                       ← Định nghĩa 1 route (method + pattern)
│   │
│   ├── MVC/
│   │   ├── Controller.php                  ← Base Controller (model(), send())
│   │   └── Model.php                       ← Base Model (kết nối Database PDO)
│   │
│   ├── Database/
│   │   ├── DatabaseAdapter.php             ← Database adapter
│   │   └── DB/
│   │       └── PDO.php                     ← PDO driver (query, escape, getLastId)
│   │
│   └── Helper/
│       └── public.php                      ← Helper functions (clean, cleanUrl)
│
├── Application/                             ← 🟡 WEBSERVICES CỤ THỂ
│   ├── Controllers/                         ← API Controllers (xử lý logic)
│   │   ├── auth.php                        ← /api/auth/login, /register, /refresh
│   │   ├── services.php                    ← /api/services (CRUD)
│   │   ├── rooms.php                       ← /api/rooms, /rooms/available/:type
│   │   ├── roomtypes.php                   ← /api/room-types (CRUD)
│   │   ├── bookings.php                    ← /api/bookings, /confirm, /checkin
│   │   ├── guests.php                      ← /api/guests (CRUD)
│   │   ├── payments.php                    ← /api/payments
│   │   ├── serviceorders.php               ← /api/bookings/:id/services
│   │   └── reports.php                     ← /api/reports/dashboard, /revenue
│   │
│   └── Models/                              ← API Models (truy vấn Database)
│       ├── auth.php                        ← Authenticate, tạo JWT token
│       ├── services.php                    ← CRUD hotelservice_services
│       ├── rooms.php                       ← CRUD rooms_room, phòng trống
│       ├── roomtypes.php                   ← CRUD rooms_roomtype
│       ├── bookings.php                    ← CRUD bookings_booking
│       ├── guests.php                      ← CRUD hotels_guests
│       ├── payments.php                    ← CRUD payments_payment
│       └── serviceorders.php               ← CRUD hotelservice_servicesused
│
├── Router/
│   └── Router.php                           ← 🟢 ĐỊNH NGHĨA 50+ ROUTES
│
└── Postman/
    └── Hotel_Management_Local.postman_environment.json
```

---

## 5. CHI TIẾT TỪNG THÀNH PHẦN

### 5.1 `index.php` — Entry Point

**Nhiệm vụ:** Phân loại request → REST API hay MVC web app

```php
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isApiRequest = (stripos($requestUri, '/api') !== false);

if ($isApiRequest) {
    // REST API MODE
    require_once __DIR__ . '/config.php';
    require_once SYSTEM . 'Startup.php';
    
    $request = new Http\Request();
    $response = new Http\Response();
    $router = new Router\Router($request->getUrl(), $request->getMethod());
    require_once __DIR__ . '/Router/Router.php';
    $router->run();
    $response->render();
    exit;
}

// MVC WEB APP MODE
require_once "bridge.php";
$app = new App();
```

### 5.2 `config.php` — Cấu Hình

**Nhiệm vụ:** Định nghĩa đường dẫn + thông tin database

```php
define('SCRIPT', str_replace('\\', '/', rtrim(__DIR__, '/')) . '/');
define('SYSTEM', SCRIPT . 'System/');
define('CONTROLLERS', SCRIPT . 'Application/Controllers/');
define('MODELS', SCRIPT . 'Application/Models/');

define('DATABASE', [
    'Port'   => '3307',
    'Host'   => '127.0.0.1',
    'Driver' => 'PDO',
    'Name'   => 'web_hotel_mngt',
    'User'   => 'root',
    'Pass'   => '',
]);
```

### 5.3 `Http/Request.php` — Xử Lý Request

**Các method quan trọng:**

| Method | Trả về | Ví dụ |
|--------|--------|-------|
| `getMethod()` | HTTP method | `'GET'`, `'POST'`, `'PUT'`, `'DELETE'` |
| `getUrl()` | Request URI | `'/Hotel_Management_Website/api/health'` |
| `input()` | JSON body | `['username'=>'admin','password'=>'123456']` |
| `input('key')` | Giá trị cụ thể | `'admin'` |
| `server('KEY')` | $_SERVER value | `'GET'` (REQUEST_METHOD) |

### 5.4 `Http/Response.php` — Xử Lý Response

**Các method quan trọng:**

| Method | Tác dụng |
|--------|----------|
| `setHeader($h)` | Thêm HTTP header |
| `setContent($data)` | Mã hóa `$data` thành JSON |
| `sendStatus($code)` | Set HTTP status code (200, 404...) |
| `render()` | Xuất headers + JSON ra client |

### 5.5 `Router/Router.php` — Router

**Cơ chế hoạt động:**

1. **Đăng ký route:**
   ```php
   $router->get('/api/services', 'services@index');
   $router->post('/api/bookings', 'bookings@store');
   ```

2. **So khớp URL:**
   - Chuyển pattern `/api/services/:id` thành regex
   - `preg_match` so khớp với request URL
   - Nếu khớp → Lấy params (`:id` → value)

3. **Gọi Controller:**
   - Tách `'services@index'` → Controller = `services`, Method = `index`
   - Load file: `Application/Controllers/services.php`
   - Tạo object: `new ControllersServices()`
   - Gọi method: `$controller->index($params)`

### 5.6 `MVC/Controller.php` — Base Controller

```php
class Controller {
    public $request;
    public $response;
    
    public function __construct() {
        $this->request = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];
    }
    
    public function model($model) {
        // Load: Application/Models/{model}.php
        // Tạo: Models{model} object
    }
    
    public function send($status, $msg, $data = null) {
        $this->response->setHeader("HTTP/1.1 $status ...");
        $this->response->setContent([
            'success' => $status < 400,
            'message' => $msg,
            'data' => $data
        ]);
    }
}
```

### 5.7 `MVC/Model.php` — Base Model

```php
class Model {
    public $db;
    
    public function __construct() {
        // Tự động kết nối database
        $this->db = new \Database\DatabaseAdapter(
            DATABASE['Driver'], DATABASE['Host'],
            DATABASE['User'], DATABASE['Pass'],
            DATABASE['Name'], DATABASE['Port']
        );
    }
}
```

### 5.8 `Database/DB/PDO.php` — PDO Driver

```php
// Query:
$result = $this->db->query("SELECT * FROM hotelservice_services");
// Trả về: stdClass { row, rows, num_rows }
// $result->rows → mảng tất cả kết quả
// $result->row  → kết quả đầu tiên
// $result->num_rows → số dòng

// Escape (chống SQL injection):
$safe = $this->db->escape($userInput);

// Last insert ID:
$id = $this->db->getLastId();
```

---

## 6. DANH SÁCH API ENDPOINTS

### Public (Không cần Auth)

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/health` | Health check |
| GET | `/api/services` | Lấy tất cả dịch vụ |
| GET | `/api/services/:id` | Lấy 1 dịch vụ |
| GET | `/api/rooms` | Lấy tất cả phòng |
| GET | `/api/rooms/available/:type` | Lấy phòng trống theo loại |
| GET | `/api/rooms/:id` | Lấy 1 phòng |
| GET | `/api/room-types` | Lấy tất cả loại phòng |
| GET | `/api/room-types/:id` | Lấy 1 loại phòng |
| POST | `/api/auth/login` | Đăng nhập |
| POST | `/api/auth/register` | Đăng ký |

### Protected (Cần JWT Token)

| Method | Endpoint | Role | Mô tả |
|--------|----------|------|-------|
| GET | `/api/bookings` | admin, employee | Lấy tất cả booking |
| GET | `/api/bookings/:id` | Any | Lấy 1 booking |
| POST | `/api/bookings` | Any | Tạo booking |
| PUT | `/api/bookings/:id` | admin, employee | Sửa booking |
| DELETE | `/api/bookings/:id` | admin, employee | Hủy booking |
| POST | `/api/bookings/:id/confirm` | admin, employee | Xác nhận + gán phòng |
| POST | `/api/bookings/:id/checkin` | admin, employee | Check-in |
| POST | `/api/bookings/:id/cancel` | Any | Hủy booking |
| POST | `/api/services` | admin, employee | Tạo dịch vụ |
| PUT | `/api/services/:id` | admin, employee | Sửa dịch vụ |
| DELETE | `/api/services/:id` | admin | Xóa dịch vụ |
| GET | `/api/guests` | admin, employee | Lấy tất cả khách |
| POST | `/api/guests` | Any | Thêm khách mới |
| PUT | `/api/guests/:id` | Any | Sửa thông tin khách |
| DELETE | `/api/guests/:id` | admin | Xóa khách |
| GET | `/api/payments` | admin, employee | Lấy tất cả thanh toán |
| POST | `/api/payments` | Any | Tạo thanh toán |
| GET | `/api/payments/booking/:id` | Any | Lấy thanh toán theo booking |
| GET | `/api/bookings/:id/services` | Any | Lấy dịch vụ của booking |
| POST | `/api/bookings/:id/services` | Any | Thêm dịch vụ vào booking |
| DELETE | `/api/bookings/:id/services/:id` | Any | Xóa dịch vụ khỏi booking |
| GET | `/api/reports/dashboard` | admin, employee | Dashboard summary |
| GET | `/api/reports/revenue` | admin | Báo cáo doanh thu |
| GET | `/api/reports/occupancy` | admin | Báo cáo tỷ lệ lấp đầy |

---

## 7. CHUẨN RESTFUL ÁP DỤNG

| Tiêu chí | Có | Ví dụ |
|----------|----|-------|
| **Resource-based URL** | ✅ | `/api/services`, `/api/rooms`, `/api/bookings` |
| **HTTP Methods đúng** | ✅ | GET=lấy, POST=tạo, PUT=sửa, DELETE=xóa |
| **JSON Response** | ✅ | `{"success":true,"message":"...","data":[...]}` |
| **HTTP Status Codes** | ✅ | 200, 201, 400, 401, 404, 409, 500 |
| **Stateless** | ✅ | Mỗi request độc lập, không dùng session |
| **CORS** | ✅ | `Access-Control-Allow-Origin: *` |
| **Noun-based URL** | ✅ | `/api/services` (không phải `/api/getServices`) |
| **Plural resources** | ✅ | `/api/services` (không phải `/api/service`) |
| **Nested resources** | ✅ | `/api/bookings/:id/services` |
| **Query params lọc** | ✅ | `/api/rooms/available?type=LP001` |
| **Không framework ngoài** | ✅ | Tự viết từ đầu 100% |

---

## 8. AUTHENTICATION & AUTHORIZATION

### 8.1 JWT Token Flow

```
1. Client → POST /api/auth/login
   Body: {"username":"admin","password":"123456"}

2. Server → Query DB → Nếu đúng → Tạo JWT token:
   
   Header:  {"typ":"JWT","alg":"HS256"}
   Payload: {"user_id":"AD001","username":"admin","role":"admin",
             "iat":1712750000,"exp":1712753600}
   Signature: HMACSHA256(base64(header) + "." + base64(payload), secret)
   
   Token = base64(header).base64(payload).base64(signature)
   → Trả về client

3. Client → Lưu token → Gửi kèm các request sau:
   Header: Authorization: Bearer eyJ0eXAi...

4. Server → Nhận request → Giải mã token → Lấy user info → Xử lý
```

### 8.2 Authorization trong Controller

```php
class ControllersServices extends Controller {
    public function store() {
        // Yêu cầu user đã đăng nhập và có role admin hoặc employee
        $user = $this->authorize(['admin', 'employee']);
        if (!$user) return; // → Tự động trả 401 hoặc 403
        
        // Tiếp tục xử lý...
    }
}
```

### 8.3 Roles

| Role | Mô tả | Quyền hạn |
|------|-------|-----------|
| `admin` | Quản trị viên | Full access (CRUD mọi resource) |
| `employee` | Nhân viên | Đọc + Sửa (không xóa) |
| `guest` | Khách hàng | Tạo booking, xem thông tin cá nhân |

---

## 9. DATABASE SCHEMA

### Bảng chính

| Bảng | Mô tả | Cột chính |
|------|-------|-----------|
| `authentication_admin` | Tài khoản admin | MaDangNhap, TenDangNhap, MatKhau |
| `authentication_login` | Tài khoản nhân viên | MaDangNhap, TenDangNhap, MatKhau, MaNhanVien |
| `hotels_guests` | Khách hàng | MaKhachHang, HoKhachHang, TenKhachHang, SoDienThoaiKhachHang |
| `hotelservice_services` | Dịch vụ | MaDichVu, TenDichVu, MoTaDichVu, ChiPhiDichVu |
| `hotelservice_servicesused` | Dịch vụ đã dùng | MaDichVuSuDung, MaDatPhong, MaDichVu, SoLuong, DonGia, ThanhTien |
| `rooms_room` | Phòng | MaPhong, SoPhong, MaLoaiPhong, KhaDung |
| `rooms_roomtype` | Loại phòng | MaLoaiPhong, TenLoaiPhong, GiaPhong, MoTaLoaiPhong |
| `rooms_roombooked` | Phòng đã đặt | MaPhongDaDat, MaDatPhong, MaPhong |
| `bookings_booking` | Đặt phòng | MaDatPhong, NgayDatPhong, NgayNhanPhong, NgayTraPhong, MaKhachHang, TrangThai |
| `payments_payment` | Thanh toán | MaThanhToan, MaDatPhong, TienPhong, TienDichVu, TongTien, PhuongThuc |

---

## 10. VÍ DỤ REQUEST/RESPONSE

### 10.1 Health Check

**Request:**
```http
GET /Hotel_Management_Website/api/health HTTP/1.1
Host: localhost
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "OK",
  "data": {
    "time": "2026-04-10T11:18:18+02:00"
  }
}
```

### 10.2 Login

**Request:**
```http
POST /Hotel_Management_Website/api/auth/login HTTP/1.1
Host: localhost
Content-Type: application/json

{
  "username": "admin",
  "password": "123456"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": "AD001",
      "username": "admin",
      "role": "admin",
      "display_name": "Administrator"
    }
  }
}
```

### 10.3 Create Booking

**Request:**
```http
POST /Hotel_Management_Website/api/bookings HTTP/1.1
Host: localhost
Content-Type: application/json

{
  "MaKhachHang": "KH001",
  "NgayNhanPhong": "2026-05-01",
  "NgayTraPhong": "2026-05-03",
  "MaLoaiPhong": "LP001",
  "GhiChu": "Xin cho nhận phòng sớm"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Booking created",
  "data": {
    "MaDatPhong": "DP20260410123456789",
    "NgayDatPhong": "2026-04-10",
    "NgayNhanPhong": "2026-05-01",
    "NgayTraPhong": "2026-05-03",
    "MaKhachHang": "KH001",
    "ThoiGianLuuTru": 2,
    "SoTienDatPhong": 1000000,
    "TrangThai": "Pending"
  }
}
```

### 10.4 Error Response

**Request:**
```http
GET /Hotel_Management_Website/api/services/NOTEXIST HTTP/1.1
```

**Response (404 Not Found):**
```json
{
  "error": "Route Not Found",
  "status_code": 404
}
```

---

## 11. CÁCH TEST & DEBUG

### Test trong trình duyệt

```
Health:    http://localhost/Hotel_Management_Website/api/health
Services:  http://localhost/Hotel_Management_Website/api/services
Rooms:     http://localhost/Hotel_Management_Website/api/rooms
Bookings:  http://localhost/Hotel_Management_Website/api/bookings
```

### Test với cURL

```bash
# Health check
curl http://localhost/Hotel_Management_Website/api/health

# Login
curl -X POST http://localhost/Hotel_Management_Website/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"123456"}'

# Get services (có token)
curl http://localhost/Hotel_Management_Website/api/services \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test với Postman

1. Import file: `Postman/Hotel_Management_Local.postman_environment.json`
2. Chọn Environment: **Hotel Management Local**
3. base_url = `http://localhost/Hotel_Management_Website/api`
4. Test lần lượt: Health → Login → Services → Rooms → Bookings

### Debug khi gặp lỗi

| Lỗi | Kiểm tra | Sửa |
|-----|----------|-----|
| Ra HTML | Apache đang đọc file cũ | Copy code sang `D:\xampp\htdocs\Hotel_Management_Website\` |
| 401 Unauthorized | Chưa có admin trong DB | `INSERT INTO authentication_admin ...` |
| 404 Not Found | Sai URL hoặc route chưa đăng ký | Kiểm tra `Router/Router.php` |
| 500 Error | Lỗi PHP hoặc DB | Xem Apache error log |
| Token hết hạn | Token chỉ có hiệu lực 1 giờ | Login lại để lấy token mới |

---

## 🏁 KẾT LUẬN

Hệ thống RESTful API được xây dựng **hoàn toàn từ đầu** theo kiến trúc của [afgprogrammer/PHP-MVC-REST-API](https://github.com/afgprogrammer/PHP-MVC-REST-API):

- ✅ **50+ endpoints** cho tất cả resources
- ✅ **CRUD đầy đủ** cho Services, Rooms, Bookings, Guests, Payments
- ✅ **JWT Authentication** với Bearer token
- ✅ **Role-based Authorization** (admin, employee, guest)
- ✅ **JSON responses** với HTTP status codes chuẩn
- ✅ **CORS support** cho frontend khác domain
- ✅ **Self-built framework** — Không dùng thư viện ngoài

**Tất cả tự viết từ đầu — Đúng chuẩn RESTful API!** 🚀
