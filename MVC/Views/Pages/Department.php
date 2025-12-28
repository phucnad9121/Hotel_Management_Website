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
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <button class="btn btn-primary" onclick="toggleForm(true)">
                        <i class="fas fa-plus-circle"></i> Thêm mới
                    </button>
                    
                    <form action="?controller=DepartmentController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" placeholder="Tìm tên hoặc mã..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="right-tools" style="display: flex; gap: 10px;">
                    <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                    <a href="?controller=DepartmentController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                    </a>
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                       <i class="fas fa-file-import"></i> Upload File
                    </button>
                </div>
            </div>

            <div id="departmentForm" class="info-card" style="display: none; margin-top: 20px;">
                <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm bộ phận mới</h4>
                <form id="mainDepForm" action="?controller=DepartmentController&action=saveDepartment" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Mã Bộ Phận</label>
                            <input type="text" name="MaBoPhan" id="MaBoPhan" class="form-control" placeholder="VD: BP01" required>
                        </div>
                        <div class="form-group">
                            <label>Tên Bộ Phận</label>
                            <input type="text" name="TenBoPhan" id="TenBoPhan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Lương Khởi Điểm</label>
                            <input type="number" name="LuongKhoiDiem" id="LuongKhoiDiem" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Chức Danh</label>
                            <input type="text" name="ChucDanh" id="ChucDanh" class="form-control">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Mô Tả</label>
                            <input type="text" name="MoTaBoPhan" id="MoTaBoPhan" class="form-control">
                        </div>
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <input type="hidden" name="isEdit" id="isEdit" value="0">
                        <button type="submit" class="btn btn-primary">Xác nhận Lưu</button>
                        <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy bỏ</button>
                    </div>
                </form>
            </div>

            <div id="importForm" class="info-card" style="display: none; margin-top: 20px; border: 2px dashed #38bdf8; background: #1e293b;">
                <h4 style="color: #38bdf8; margin-bottom: 15px;">
                    <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
                </h4>
                <form action="?controller=DepartmentController&action=importExcel" method="POST" enctype="multipart/form-data">
                    <div style="text-align: center; padding: 30px; border-radius: 8px; background: #2d333b;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: #38bdf8; margin-bottom: 10px;"></i>
                        <p style="color: #ccc; margin-bottom: 15px;">Chọn file Excel (.xlsx) có cấu trúc chuẩn để tải lên hệ thống</p>
                        
                        <input type="file" name="excel_file" id="excel_file_input" required style="margin-bottom: 20px; color: white;">
                        
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button type="submit" class="btn btn-primary">Bắt đầu tải lên</button>
                            <button type="button" class="btn btn-danger" onclick="toggleImport(false)">Hủy bỏ</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container" style="margin-top: 20px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã BP</th>
                            <th>Tên Bộ Phận</th>
                            <th>Mô Tả</th>
                            <th>Lương Khởi Điểm</th>
                            <th>Chức Danh</th>
                            <th style="text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['departments'])): ?>
                            <?php foreach($data['departments'] as $row): ?>
                            <tr>
                                <td><strong><?= $row['MaBoPhan'] ?></strong></td>
                                <td><?= $row['TenBoPhan'] ?></td>
                                <td><?= $row['MoTaBoPhan'] ?></td>
                                <td class="salary-text"><?= number_format($row['LuongKhoiDiem']) ?> đ</td>
                                <td><span class="badge"><?= $row['ChucDanh'] ?></span></td>
                                <td class="action-buttons">
                                    <button class="btn-icon edit" onclick="editDep('<?= $row['MaBoPhan'] ?>','<?= $row['TenBoPhan'] ?>','<?= $row['MoTaBoPhan'] ?>',<?= $row['LuongKhoiDiem'] ?>,'<?= $row['ChucDanh'] ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?controller=DepartmentController&action=deleteDepartment&id=<?= $row['MaBoPhan'] ?>" class="btn-icon delete" onclick="return confirm('Xóa bộ phận này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">Không tìm thấy dữ liệu bộ phận phù hợp.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
/**
 * Hàm quản lý trạng thái hiển thị của UI
 * @param activeForm: 'add' | 'import' | null
 */
function manageDisplay(activeForm) {
    const tables = document.querySelectorAll('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('departmentForm');
    const importForm = document.getElementById('importForm');

    // 1. Ẩn tất cả trước khi hiển thị cái mới
    tables.forEach(t => t.style.display = 'none');
    if(toolbar) toolbar.style.display = 'none';
    if(addForm) addForm.style.display = 'none';
    if(importForm) importForm.style.display = 'none';

    // 2. Kiểm tra trạng thái để hiển thị lại
    if (activeForm === 'add') {
        addForm.style.display = 'block';
    } else if (activeForm === 'import') {
        importForm.style.display = 'block';
    } else {
        // Mặc định hiện lại bảng và thanh công cụ
        tables.forEach(t => t.style.display = 'block');
        if(toolbar) toolbar.style.display = 'flex';
    }
}

// Bật/Tắt Form Import
function toggleImport(show) {
    manageDisplay(show ? 'import' : null);
}

// Bật/Tắt Form Thêm mới
function toggleForm(show = true) {
    if (show) {
        manageDisplay('add');
        // Reset trạng thái về chế độ THÊM
        document.getElementById('isEdit').value = "0";
        document.getElementById('MaBoPhan').readOnly = false;
        document.getElementById('formTitle').innerText = "Thêm Bộ Phận Mới";
        
        const formObj = document.getElementById('mainDepForm');
        if(formObj) formObj.reset();
    } else {
        manageDisplay(null);
    }
}

// Chế độ Sửa (Dùng chung UI với Thêm)
function editDep(id, name, desc, salary, title) {
    manageDisplay('add'); 
    
    document.getElementById('formTitle').innerText = "Chỉnh sửa bộ phận: " + id;
    document.getElementById('isEdit').value = "1";
    document.getElementById('MaBoPhan').value = id;
    document.getElementById('MaBoPhan').readOnly = true;
    document.getElementById('TenBoPhan').value = name;
    document.getElementById('MoTaBoPhan').value = desc;
    document.getElementById('LuongKhoiDiem').value = salary;
    document.getElementById('ChucDanh').value = title;
    
    // Cuộn lên đầu trang mượt mà để thấy Form
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>