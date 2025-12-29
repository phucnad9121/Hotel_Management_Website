<div class="login-wrapper">
    <div class="login-container">
        
        <div class="login-image">
            <div class="image-overlay">
                <h2>Luxury Hotel</h2>
                <p>Trải nghiệm đẳng cấp 5 sao</p>
            </div>
        </div>

        <div class="login-right-side">
            <div class="login-header">
                <i class="fas fa-hotel"></i>
                <h1>ĐĂNG NHẬP HỆ THỐNG</h1>
            </div>

            <form action="?controller=AuthController&action=handleLogin" method="POST" class="login-form">
                <div class="input-group">
                    <label for="account_type"><i class="fas fa-user-tag"></i> Loại tài khoản:</label>
                    <select name="account_type" id="account_type" class="form-control">
                        <option value="khach_hang">Khách hàng</option>
                        <option value="nhan_vien">Nhân viên</option>
                        <option value="quan_tri">Quản trị viên</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="username"><i class="fas fa-user"></i> Tên đăng nhập:</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Nhập tài khoản..." required>
                </div>

                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu:</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>

                <div class="login-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </button>
                    <a href="?controller=AuthController&action=index" class="btn-back">Quay lại</a>
                </div>

                <p class="login-note">
                    <i class="fas fa-info-circle"></i> 
                    Khách hàng: Dùng số điện thoại để đăng nhập
                </p>
                
                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <p style="color: var(--text-muted); margin-bottom: 10px;">Chưa có tài khoản?</p>
                    <a href="?controller=GuestController&action=register" 
                       style="color: var(--ocean-blue); text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fas fa-user-plus"></i> Đăng ký tài khoản khách hàng
                    </a>
                </div>
            </form>
        </div> </div>
</div>