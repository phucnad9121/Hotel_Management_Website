<style>
.guest-home {
    background: var(--bg-dark);
    min-height: 100vh;
}
.guest-header {
    background: linear-gradient(135deg, var(--ocean-blue), #0369a1);
    color: white;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.hero-section {
    position: relative;
    height: 500px;
    background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), 
                url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200') center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
}
.hero-content h1 {
    font-size: 3rem;
    margin-bottom: 15px;
}
.room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    padding: 40px;
    max-width: 1400px;
    margin: 0 auto;
}
.room-card {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s;
    border: 1px solid var(--border-color);
}
.room-card:hover {
    transform: translateY(-5px);
}
.room-image {
    height: 220px;
    background-size: cover;
    background-position: center;
    position: relative;
}
.room-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--ocean-blue);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: bold;
}
.room-info {
    padding: 20px;
    color: var(--text-white);
}
.room-info h3 {
    margin: 0 0 10px 0;
    color: var(--ocean-blue);
}
.room-price {
    font-size: 1.5rem;
    color: #10b981;
    font-weight: bold;
    margin: 10px 0;
}
.btn-book {
    width: 100%;
    padding: 12px;
    background: var(--ocean-blue);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 10px;
    transition: background 0.3s;
}
.btn-book:hover {
    background: #0369a1;
}
.btn-book:disabled {
    background: #666;
    cursor: not-allowed;
}
</style>

<div class="guest-home">
    <header class="guest-header">
    <div>
        <h2 style="margin: 0;"><i class="fas fa-hotel"></i> Hotel Luxury</h2>
        <p style="opacity: 0.9; margin: 5px 0 0 0;">Xin chào, <?= $data['guest']['HoKhachHang'] ?> <?= $data['guest']['TenKhachHang'] ?></p>
    </div>
    <div style="display: flex; gap: 15px;">
        <a href="?controller=GuestController&action=myBookings" class="btn-custom-white">
            <i class="fas fa-calendar-alt"></i> Booking của tôi
        </a>
        <a href="?controller=AuthController&action=logout" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</header>

    <section class="hero-section">
        <div class="hero-content">
            <h1>Chào mừng đến với Hotel Luxury</h1>
            <p style="font-size: 1.2rem;">Trải nghiệm nghỉ dưỡng đẳng cấp 5 sao</p>
        </div>
    </section>

    <div style="text-align: center; padding: 40px 20px 20px;">
        <h2 style="color: var(--text-white); font-size: 2rem;">CÁC LOẠI PHÒNG</h2>
        <p style="color: var(--text-muted);">Chọn phòng phù hợp với nhu cầu của bạn</p>
    </div>

    <div class="room-grid">
        <?php foreach($data['roomTypes'] as $room): ?>
        <div class="room-card">
            <div class="room-image" style="background-image: url('https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=500');">
                <span class="room-badge"><?= $room['SoPhongTrong'] ?> phòng trống</span>
            </div>
            <div class="room-info">
                <h3><?= $room['TenLoaiPhong'] ?></h3>
                <p style="color: var(--text-muted); margin: 10px 0;"><?= $room['MoTaPhong'] ?></p>
                <div class="room-price"><?= number_format($room['GiaPhong']) ?> VNĐ/đêm</div>
                
                <?php if($room['SoPhongTrong'] > 0): ?>
                    <a href="?controller=BookingController&action=create&type=<?= $room['MaLoaiPhong'] ?>">
                        <button class="btn-book">
                            <i class="fas fa-calendar-check"></i> Đặt phòng ngay
                        </button>
                    </a>
                <?php else: ?>
                    <button class="btn-book" disabled>
                        <i class="fas fa-times-circle"></i> Hết phòng
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>