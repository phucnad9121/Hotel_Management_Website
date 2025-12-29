

    <main class="main-content">
        <header class="top-header">
            <div class="user-info">
                Chào mừng: <strong>Quản trị viên</strong>
            </div>
        </header>
        
        <section class="content-body">
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <form action="?controller=GuestController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" 
                               placeholder="Tìm theo tên, SĐT, CMND..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 300px;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>

                <div class="right-tools">
                    <button class="btn-custom-white" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Làm mới
                    </button>
                </div>
            </div>

            <div class="info-card" style="margin-bottom: 20px; padding: 15px; background: rgba(52, 152, 219, 0.1); border-left: 4px solid var(--ocean-blue);">
                <p style="margin: 0; color: var(--text-white);">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Lưu ý:</strong> Danh sách này hiển thị tất cả khách hàng đã đăng ký tài khoản trong hệ thống.
                    Mật khẩu không được hiển thị vì lý do bảo mật.
                </p>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã KH</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>CMND/CCCD</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng ký</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['guests'])): ?>
                            <?php foreach($data['guests'] as $row): ?>
                            <tr>
                                <td><strong>#<?= $row['MaKhachHang'] ?></strong></td>
                                <td><?= $row['HoKhachHang'] ?> <?= $row['TenKhachHang'] ?></td>
                                <td><?= $row['EmailKhachHang'] ?></td>
                                <td><?= $row['SoDienThoaiKhachHang'] ?></td>
                                <td><?= $row['CMND_CCCDKhachHang'] ?></td>
                                <td><?= $row['DiaChi'] ?></td>
                                <td>
                                    <?php 
                                    $statusColor = $row['TrangThai'] == 'Reserved' ? '#e74c3c' : '#27ae60';
                                    $statusText = $row['TrangThai'] == 'Reserved' ? 'Đang đặt phòng' : 'Không đặt phòng';
                                    ?>
                                    <span class="badge" style="background: <?= $statusColor ?>; color: white; border: none;">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['NgayTao'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align:center;">Không tìm thấy khách hàng nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(!empty($data['guests'])): ?>
            <div class="info-card" style="margin-top: 20px; text-align: center;">
                <p style="margin: 0; color: var(--text-white); font-size: 1.1rem;">
                    <i class="fas fa-users"></i> 
                    Tổng số khách hàng: <strong style="color: var(--ocean-blue);"><?= count($data['guests']) ?></strong>
                </p>
            </div>
            <?php endif; ?>
        </section>
    </main>
