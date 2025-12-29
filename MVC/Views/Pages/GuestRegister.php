<div class="login-wrapper">
    <div class="login-container" style="width: 550px;">
        <div class="login-header">
            <i class="fas fa-user-plus"></i>
            <h1>ĐĂNG KÝ TÀI KHOẢN KHÁCH HÀNG</h1>
        </div>

        <form action="?controller=GuestController&action=handleRegister" method="POST" class="login-form">
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label for="ho"><i class="fas fa-user"></i> Họ:</label>
                    <input type="text" name="ho" id="ho" class="form-control" required>
                </div>

                <div class="input-group">
                    <label for="ten"><i class="fas fa-user"></i> Tên:</label>
                    <input type="text" name="ten" id="ten" class="form-control" required>
                </div>

                <div class="input-group">
                    <label for="sdt"><i class="fas fa-phone"></i> Số điện thoại:</label>
                    <input type="text" name="sdt" id="sdt" class="form-control" required>
                </div>

                <div class="input-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="input-group" style="grid-column: span 2;">
                    <label for="cmnd"><i class="fas fa-id-card"></i> CMND/CCCD:</label>
                    <input type="text" name="cmnd" id="cmnd" class="form-control" required>
                </div>

                <div class="input-group" style="grid-column: span 2;">
                    <label for="diachi"><i class="fas fa-map-marker-alt"></i> Địa chỉ:</label>
                    <input type="text" name="diachi" id="diachi" class="form-control" required>
                </div>

                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="input-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Xác nhận mật khẩu:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
            </div>

            <div class="login-actions" style="margin-top: 20px;">
                <button type="submit" class="btn-submit">Đăng ký</button>
                <a href="?controller=AuthController&action=login" class="btn-back">Đã có tài khoản</a>
            </div>

            <p class="login-note">
                <i class="fas fa-info-circle"></i> 
                Bạn sẽ đăng nhập bằng số điện thoại và mật khẩu
            </p>
        </form>
    </div>
</div>