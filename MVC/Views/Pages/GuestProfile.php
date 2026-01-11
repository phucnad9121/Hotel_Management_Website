<style>
    /* --- KHỐI STYLE ĐỒNG BỘ VỚI GUEST HOME (DARK THEME) --- */
    :root {
        --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        --bg-dark: #0f172a;       /* Màu nền tối của web */
        --card-bg: #1e293b;       /* Màu nền card */
        --input-bg: #334155;      /* Màu nền ô input */
        --text-white: #f8fafc;
        --text-muted: #94a3b8;
        --border-color: #334155;
    }

    .guest-profile-page {
        background-color: var(--bg-dark);
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
        padding-bottom: 50px;
    }

    /* 1. Header Menu (Giống hệt GuestMyBookings) */
    .guest-header {
        background: var(--card-bg);
        border-bottom: 1px solid var(--border-color);
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .guest-header h2 { color: var(--text-white); font-size: 1.5rem; margin: 0; }
    .guest-header p { color: #38bdf8; margin: 0; font-size: 0.9rem; }

    /* 2. Layout Grid */
    .profile-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 350px 1fr; /* Cột trái nhỏ, cột phải to */
        gap: 30px;
    }

    /* 3. Sidebar Card (Cột trái) */
    .sidebar-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        border: 1px solid var(--border-color);
    }

    .avatar-circle {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    .user-name { color: var(--text-white); font-size: 1.4rem; font-weight: bold; margin-bottom: 5px; }
    .user-role { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px; }

    .side-menu { list-style: none; padding: 0; text-align: left; margin-top: 30px; }
    .side-menu li { margin-bottom: 10px; }
    .side-menu a {
        display: block;
        padding: 12px 15px;
        color: var(--text-muted);
        border-radius: 8px;
        transition: all 0.3s;
        text-decoration: none;
        font-weight: 500;
    }
    .side-menu a:hover, .side-menu a.active {
        background: rgba(14, 165, 233, 0.1);
        color: #38bdf8; /* Light blue */
        padding-left: 20px;
    }
    .side-menu i { margin-right: 10px; width: 20px; text-align: center; }

    /* 4. Main Form Card (Cột phải) */
    .main-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 40px;
        border: 1px solid var(--border-color);
    }

    .card-title {
        color: var(--text-white);
        font-size: 1.5rem;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    /* Style cho Form Input */
    .form-group { margin-bottom: 20px; }
    .form-label { color: #cbd5e1; font-weight: 500; margin-bottom: 8px; display: block; }
    
    .custom-input {
        width: 100%;
        padding: 12px 15px;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: white;
        transition: border 0.3s;
    }
    .custom-input:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
    }
    .custom-input[readonly] {
        background: #0f172a; /* Darker bg for readonly */
        color: #64748b;
        cursor: not-allowed;
    }

    .btn-save {
        background: var(--primary-gradient);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s;
        width: 100%;
        margin-top: 20px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4); }

    /* Nút Header */
    .btn-custom-white {
        background: rgba(255,255,255,0.1);
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.3s;
    }
    .btn-custom-white:hover { background: rgba(255,255,255,0.2); }
    .btn-logout {
        background: #ef4444;
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
    }
</style>

<div class="guest-profile-page">
    
    <header class="guest-header">
        <div>
            <h2><i class="fas fa-hotel"></i> Hotel Luxury</h2>
            <p>Trải nghiệm đẳng cấp 5 sao</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="?controller=GuestController&action=home" class="btn-custom-white">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <a href="?controller=GuestController&action=myBookings" class="btn-custom-white">
                <i class="fas fa-calendar-check"></i> Booking của tôi
            </a>
            <a href="?controller=AuthController&action=logout" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </header>

    <div class="profile-container">
        
        <aside class="sidebar-card">
            <div class="avatar-circle">
                <?= strtoupper(substr($data['guest']['TenKhachHang'], 0, 1)) ?>
            </div>
            
            <h3 class="user-name"><?= $data['guest']['HoKhachHang'] . ' ' . $data['guest']['TenKhachHang'] ?></h3>
            <p class="user-role">Thành viên thân thiết</p>
            <p style="color: #38bdf8; font-size: 0.9rem;">
                <i class="fas fa-clock"></i> Tham gia: <?= date('d/m/Y', strtotime($data['guest']['NgayTao'])) ?>
            </p>

            <ul class="side-menu">
                <li><a href="#" class="active"><i class="fas fa-user-circle"></i> Thông tin tài khoản</a></li>
                <li><a href="?controller=GuestController&action=myBookings"><i class="fas fa-history"></i> Lịch sử đặt phòng</a></li>
                <li><a href="?controller=AuthController&action=logout" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="main-card">
            <h2 class="card-title"><i class="fas fa-edit"></i> Chỉnh Sửa Hồ Sơ</h2>
            
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Họ đệm</label>
                        <input type="text" name="ho" class="custom-input" value="<?= $data['guest']['HoKhachHang'] ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Tên</label>
                        <input type="text" name="ten" class="custom-input" value="<?= $data['guest']['TenKhachHang'] ?>" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="custom-input" value="<?= $data['guest']['EmailKhachHang'] ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Số điện thoại (*)</label>
                        <input type="number" name="sdt" class="custom-input" value="<?= $data['guest']['SoDienThoaiKhachHang'] ?>" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label class="form-label">CMND / CCCD</label>
                        <?php $readonly = $data['hasBooking'] ? "readonly" : ""; ?>
                        <input type="text" name="cmnd" class="custom-input" value="<?= $data['guest']['CMND_CCCDKhachHang'] ?>" <?= $readonly ?>>
                        <?php if($data['hasBooking']): ?>
                            <small style="color: #ef4444; font-size: 0.8rem; margin-top: 5px; display:block;">
                                <i class="fas fa-lock"></i> Đã khóa do có đơn đặt phòng.
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="diachi" class="custom-input" value="<?= $data['guest']['DiaChi'] ?>">
                    </div>
                </div>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
                    <h4 style="color: var(--text-white); margin-bottom: 20px; font-size: 1.1rem;">
                        <i class="fas fa-shield-alt" style="color: #38bdf8;"></i> Bảo mật (Đổi mật khẩu)
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Mật khẩu hiện tại (Bắt buộc nếu đổi mới)</label>
                        <input type="password" name="current_password" class="custom-input" placeholder="********">
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="custom-input" placeholder="Để trống nếu không đổi">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" class="custom-input" placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> LƯU THAY ĐỔI
                </button>
            </form>
        </main>
    </div>
</div>