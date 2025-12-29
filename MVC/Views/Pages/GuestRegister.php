<div class="login-wrapper">
    <div class="login-container">
        
        <div class="login-image">
            <div class="image-overlay">
                <h2>Thành viên mới</h2>
                <p>Đăng ký để nhận nhiều ưu đãi hấp dẫn</p>
            </div>
        </div>

        <div class="login-right-side">
            <div class="login-header">
                <i class="fas fa-user-plus"></i>
                <h1>ĐĂNG KÝ TÀI KHOẢN</h1>
            </div>

            <form action="?controller=GuestController&action=handleRegister" method="POST" class="login-form">
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    
                    <div class="input-group">
                        <label for="ho">Họ:</label>
                        <input type="text" name="ho" id="ho" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label for="ten">Tên:</label>
                        <input type="text" name="ten" id="ten" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label for="sdt">Số điện thoại:</label>
                        <input type="text" name="sdt" id="sdt" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="input-group" style="grid-column: span 2;">
                        <label for="diachi">Địa chỉ:</label>
                        <input type="text" name="diachi" id="diachi" class="form-control" required>
                    </div>

                    <div class="input-group" style="grid-column: span 2;">
                        <label for="cmnd">CMND/CCCD:</label>
                        <input type="text" name="cmnd" id="cmnd" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Mật khẩu:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Xác nhận:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                </div>

                <div class="login-actions" style="margin-top: 25px;">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Đăng ký
                    </button>
                    <a href="?controller=AuthController&action=login" class="btn-back">
                        Đã có tài khoản
                    </a>
                </div>

                <p class="login-note">
                    <i class="fas fa-info-circle"></i> 
                    Bạn sẽ dùng Số điện thoại để đăng nhập
                </p>
            </form>
        </div>
    </div>
</div>