

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
