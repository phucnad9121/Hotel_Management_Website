<style>
.service-list-wrapper {
    min-height: 100vh;
    background: var(--bg-dark);
    padding: 40px 20px;
}
.service-item-card {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 3px solid var(--ocean-blue);
}
.service-item-info h4 {
    color: var(--text-white);
    margin: 0 0 8px 0;
}
.service-item-info p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}
.service-item-price {
    text-align: right;
}
.service-item-price .price {
    font-size: 1.5rem;
    color: #10b981;
    font-weight: bold;
}
.service-item-price .time {
    color: var(--text-muted);
    font-size: 0.85rem;
}
</style>

<div class="service-list-wrapper">
    <div style="max-width: 900px; margin: 0 auto;">
        <!-- Header -->
        <div style="background: var(--card-bg); padding: 25px; border-radius: 12px; margin-bottom: 30px;">
            <h2 style="color: var(--ocean-blue); margin: 0 0 10px 0;">
                <i class="fas fa-concierge-bell"></i> Dịch vụ đã sử dụng
            </h2>
            <p style="color: var(--text-muted); margin: 0;">
                Booking #<?= $data['booking']['MaDatPhong'] ?>
            </p>
        </div>

        <!-- Danh sách dịch vụ -->
        <?php if(!empty($data['services'])): ?>
            <?php foreach($data['services'] as $service): ?>
            <div class="service-item-card">
                <div class="service-item-info">
                    <h4>
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        <?= $service['TenDichVu'] ?>
                    </h4>
                    <p><?= $service['MoTaDichVu'] ?></p>
                </div>
                <div class="service-item-price">
                    <div class="price"><?= number_format($service['ChiPhiDichVu']) ?> đ</div>
                    <div class="time">
                        <i class="fas fa-clock"></i>
                        <?= date('d/m/Y H:i', strtotime($service['NgayGioSuDung'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Tổng cộng -->
            <div style="background: linear-gradient(135deg, var(--ocean-blue), #0369a1); color: white; padding: 25px; border-radius: 12px; margin-top: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0 0 5px 0;">Tổng chi phí dịch vụ</h3>
                        <p style="opacity: 0.8; margin: 0; font-size: 0.9rem;">
                            <?= count($data['services']) ?> dịch vụ đã sử dụng
                        </p>
                    </div>
                    <div style="font-size: 2.5rem; font-weight: bold;">
                        <?= number_format($data['totalCost']) ?> đ
                    </div>
                </div>
            </div>

            <!-- Note -->
            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid var(--ocean-blue); color: var(--text-white); padding: 15px; border-radius: 8px; margin-top: 20px;">
                <i class="fas fa-info-circle" style="color: var(--ocean-blue);"></i>
                Chi phí dịch vụ sẽ được tính vào tổng hóa đơn khi trả phòng
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--card-bg); border-radius: 12px;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3 style="color: var(--text-white);">Chưa sử dụng dịch vụ nào</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">
                    Hãy đặt dịch vụ để nâng cao trải nghiệm của bạn
                </p>
                <a href="?controller=GuestController&action=orderService&booking_id=<?= $data['booking']['MaDatPhong'] ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Đặt dịch vụ ngay
                </a>
            </div>
        <?php endif; ?>

        <!-- Nút quay lại -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="?controller=GuestController&action=myBookings" class="btn-custom-white">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách booking
            </a>
        </div>
    </div>
</div>