<div class="container" style="margin-top: 50px; margin-bottom: 50px; max-width: 900px;">
    <div class="card shadow-sm border-0">
        <div class="card-header text-white text-center py-3" style="background: linear-gradient(45deg, #1e3c72, #2a5298);">
            <h3 class="mb-0"><i class="fas fa-id-card"></i> Hồ Sơ Cá Nhân</h3>
        </div>
        
        <div class="card-body p-4">
            <form action="" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ (Last Name):</label>
                        <input type="text" name="ho" class="form-control" value="<?php echo $data['guest']['HoKhachHang']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên (First Name):</label>
                        <input type="text" name="ten" class="form-control" value="<?php echo $data['guest']['TenKhachHang']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email:</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $data['guest']['EmailKhachHang']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại (*):</label>
                        <input type="number" name="sdt" class="form-control" value="<?php echo $data['guest']['SoDienThoaiKhachHang']; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">CMND / CCCD:</label>
                        <input type="text" name="cmnd" class="form-control" value="<?php echo $data['guest']['CMND_CCCDKhachHang']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ:</label>
                        <input type="text" name="diachi" class="form-control" value="<?php echo $data['guest']['DiaChi']; ?>">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 text-primary"><i class="fas fa-lock"></i> Đổi Mật Khẩu (Bỏ trống nếu không đổi)</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu mới:</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Xác nhận mật khẩu:</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới...">
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="?controller=GuestController&action=home" class="btn btn-secondary px-4">
                        <i class="fas fa-arrow-left"></i> Quay lại Trang chủ
                    </a>
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>