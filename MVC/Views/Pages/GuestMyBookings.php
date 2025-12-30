<style>
.mybookings-wrapper {
    min-height: 100vh;
    background: var(--bg-dark);
}
.booking-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    border-left: 4px solid var(--ocean-blue);
    transition: transform 0.3s;
}
.booking-card:hover {
    transform: translateX(5px);
}
.booking-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}
.booking-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}
.info-item {
    color: var(--text-muted);
}
.info-item strong {
    color: var(--text-white);
    display: block;
    margin-top: 5px;
}
.booking-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
</style>

<div class="mybookings-wrapper">
    <header class="guest-header" style="background: linear-gradient(135deg, var(--ocean-blue), #0369a1); color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0;"><i class="fas fa-hotel"></i> Hotel Luxury</h2>
            <p style="opacity: 0.9; margin: 5px 0 0 0;">Quản lý đặt phòng của tôi</p>
        </div>
        <div style="display: flex; gap: 15px;">
            <a href="?controller=GuestController&action=home" class="btn-custom-white">
                <i class="fas fa-home"></i> Trang chủ
            </a>
            <a href="?controller=AuthController&action=logout" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </header>

    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <h2 style="color: var(--text-white); margin-bottom: 25px;">
            <i class="fas fa-calendar-alt"></i> Danh sách đặt phòng
        </h2>

        <?php if(!empty($data['bookings'])): ?>
            <?php 
            $statusColors = [
                'Pending' => '#f39c12',
                'Confirmed' => '#3498db',
                'Checkin' => '#27ae60',
                'Checkout' => '#95a5a6',
                'Cancelled' => '#e74c3c'
            ];
            $statusLabels = [
                'Pending' => 'Chờ xác nhận',
                'Confirmed' => 'Đã xác nhận',
                'Checkin' => 'Đã nhận phòng',
                'Checkout' => 'Đã trả phòng',
                'Cancelled' => 'Đã hủy'
            ];
            foreach($data['bookings'] as $booking): 
                $color = $statusColors[$booking['TrangThai']] ?? '#666';
            ?>
            <div class="booking-card">
                <div class="booking-header-section">
                    <div>
                        <h3 style="color: var(--ocean-blue); margin: 0;">
                            Booking #<?= $booking['MaDatPhong'] ?>
                        </h3>
                        <small style="color: var(--text-muted);">
                            Đặt ngày: <?= date('d/m/Y H:i', strtotime($booking['NgayTao'])) ?>
                        </small>
                    </div>
                    <span class="badge" style="background: <?= $color ?>; color: white; padding: 8px 15px; border-radius: 20px;">
                        <?= $statusLabels[$booking['TrangThai']] ?>
                    </span>
                </div>

                <div class="booking-info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-check"></i> Ngày nhận phòng
                        <strong><?= date('d/m/Y', strtotime($booking['NgayNhanPhong'])) ?></strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar-times"></i> Ngày trả phòng
                        <strong><?= date('d/m/Y', strtotime($booking['NgayTraPhong'])) ?></strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-moon"></i> Số đêm
                        <strong><?= $booking['ThoiGianLuuTru'] ?> đêm</strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-door-open"></i> Phòng
                        <strong><?= $booking['SoPhong'] ?? '<span style="color: #ef4444;">Chưa gán</span>' ?></strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave"></i> Tiền phòng
                        <strong style="color: #10b981;"><?= number_format($booking['SoTienDatPhong']) ?> đ</strong>
                    </div>
                </div>

                <div class="booking-actions">
                    <?php if($booking['TrangThai'] == 'Confirmed' || $booking['TrangThai'] == 'Checkin'): ?>
                        <a href="?controller=GuestController&action=orderService&booking_id=<?= $booking['MaDatPhong'] ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Đặt dịch vụ
                        </a>
                        <a href="?controller=GuestController&action=viewMyServices&booking_id=<?= $booking['MaDatPhong'] ?>" 
                           class="btn btn-outline">
                            <i class="fas fa-list"></i> Xem dịch vụ đã đặt
                        </a>
                    <?php endif; ?>
                    
                    <?php if($booking['TrangThai'] == 'Pending'): ?>
                        <button class="btn btn-danger" onclick="if(confirm('Bạn có chắc muốn hủy booking này?')) location.href='?controller=BookingController&action=cancel&id=<?= $booking['MaDatPhong'] ?>'">
                            <i class="fas fa-times-circle"></i> Hủy đặt phòng
                        </button>
                    <?php endif; ?>
                    
                    <button class="btn-custom-white" onclick="alert('Chức năng xem chi tiết đang phát triển!')">
                        <i class="fas fa-info-circle"></i> Chi tiết
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--card-bg); border-radius: 12px;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-white);">Chưa có đặt phòng nào</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Hãy bắt đầu đặt phòng để trải nghiệm dịch vụ của chúng tôi</p>
                <a href="?controller=GuestController&action=home" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm phòng ngay
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>