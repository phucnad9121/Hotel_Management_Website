<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Khách Sạn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./Public/Css/style_hello.css">
    <link rel="stylesheet" href="./Public/Css/login_style.css">
    <link rel="stylesheet" href="./Public/Css/admin_style.css"> 
    <link rel="stylesheet" href="./Public/Css/department_style.css">
</head>
<body class="<?php echo isset($_GET['controller']) ? $_GET['controller'] : 'Default'; ?>">

    <?php 
    // Kiểm tra: Nếu controller có truyền 'page_tab' (tức là trang Admin) thì hiển thị khung Admin
    if (isset($data['page_tab'])) { 
        $tab = $data['page_tab']; // Lấy tab hiện tại để active menu
    ?>
        <div class="admin-wrapper">
            <aside class="sidebar">
                <div class="sidebar-header">
                    <i class="fas fa-hotel"></i>
                    <span>Hotel Admin</span>
                </div>
                
                <nav class="sidebar-menu">
                    <ul>
                        <li class="<?= $tab == 'dashboard' ? 'active' : '' ?>">
                           <a href="?controller=AdminController&action=index">
                           <i class="fas fa-home"></i>Trang chủ
                           </a>
                        </li>
                        <li class="<?= $tab == 'department' ? 'active' : '' ?>">
                           <a href="?controller=AdminController&action=department">
                           <i class="fas fa-th-large"></i>Quản lý Bộ phận
                           </a>
                        </li>
                        <li class="<?= $tab == 'staff' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-users"></i>Quản lý Nhân viên</a>
                        </li>
                        <li class="<?= $tab == 'account' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-user-shield"></i>Quản lý Tài khoản</a>
                        </li>
                        <li class="<?= $tab == 'roomtype' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-bed"></i>Quản lý Loại phòng</a>
                        </li>
                        <li class="<?= $tab == 'room' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-door-open"></i>Quản lý Phòng</a>
                        </li>
                        <li class="<?= $tab == 'service' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-concierge-bell"></i>Quản lý Dịch vụ</a>
                        </li>
                        <li class="<?= $tab == 'discount' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-tags"></i>Quản lý Giảm giá</a>
                        </li>
                        <li class="<?= $tab == 'guest' ? 'active' : '' ?>">
                            <a href="?controller=GuestController&action=index"><i class="fas fa-address-book"></i>Quản lý Khách hàng</a>
                        </li>
                        <li class="<?= $tab == 'booking' ? 'active' : '' ?>">
                            <a href="?controller=BookingController&action=index"><i class="fas fa-calendar-check"></i>Quản lý Đặt phòng</a>
                        </li>     
                        <li class="<?= $tab == 'payment' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-credit-card"></i>Thanh toán & Trả phòng</a>
                        </li>
                        <li class="<?= $tab == 'report' ? 'active' : '' ?>">
                            <a href="#"><i class="fas fa-chart-line"></i>Báo cáo & Thống kê</a>
                        </li>
                    </ul>
                </nav>
        
                <div class="sidebar-footer">
                    <a href="?controller=AuthController&action=logout" class="btn-logout" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?')">
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
                    <?php echo $data['content']; ?>
                </section>
            </main>
        </div>

    <?php 
    } else { 
        // Trường hợp không phải Admin (Login, Register, Guest...) -> Chỉ in content ra
        echo $data['content']; 
    } 
    ?>

</body>
</html>