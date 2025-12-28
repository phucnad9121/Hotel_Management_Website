<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hotel"></i>
            <span>Hotel Admin</span>
        </div>
        
        <nav class="sidebar-menu">
            <ul>
                <li class="active">
                   <a href="?controller=AdminController&action=department">
                   <i class="fas fa-th-large"></i>Quản lý Bộ phận
                   </a>
                </li>
                <li><a href="#"><i class="fas fa-users"></i>Quản lý Nhân viên</a></li>
                <li><a href="#"><i class="fas fa-user-shield"></i>Quản lý Tài khoản</a></li>
                <li><a href="#"><i class="fas fa-bed"></i>Quản lý Loại phòng</a></li>
                <li><a href="#"><i class="fas fa-door-open"></i>Quản lý Phòng</a></li>
                <li><a href="#"><i class="fas fa-concierge-bell"></i>Quản lý Dịch vụ</a></li>
                <li><a href="#"><i class="fas fa-tags"></i>Quản lý Giảm giá</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i>Quản lý Khách hàng</a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i>Quản lý Đặt phòng</a></li>
                <li><a href="#"><i class="fas fa-credit-card"></i>Thanh toán & Trả phòng</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i>Báo cáo & Thống kê</a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
    <a href="?controller=AuthController&action=logout" class="btn-logout" onclick="return confirmLogout(event)">
        <i class="fas fa-sign-out-alt"></i> Đăng xuất
    </a>
</div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="user-info">
                Chào mừng: <strong>Quản trị viên</strong>
            </div>
        </header>
        
        <section class="content-body">
            <div class="welcome-banner">
                <h2>Hệ thống Quản trị Hotel Luxury</h2>
            </div>
            
            <div class="info-card">
                 <p>Thống kê nhanh các bộ phận và tình hình khách hàng...</p>
            </div>
        </section>
    </main>


    <script>
    function confirmLogout(event) {
    // Hiển thị hộp thoại xác nhận ngay lập tức
    const isConfirmed = confirm("Bạn có chắc chắn muốn đăng xuất không?");
    
    if (isConfirmed) {
        // Nếu nhấn OK, cho phép sự kiện tiếp tục (trình duyệt sẽ đi đến href)
        return true; 
    } else {
        // Nếu nhấn Hủy, chặn sự kiện chuyển trang
        event.preventDefault();
        return false;
    }
}
</script>
</div>