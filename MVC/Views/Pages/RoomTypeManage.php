<main class="main-content">
    <header class="top-header">
        <div class="user-info">
            Chào mừng: <strong>Quản trị viên (Quản lý Loại Phòng)</strong>
        </div>
    </header>

    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="left-tools">
                <button class="btn btn-primary" onclick="toggleForm(true)">
                    <i class="fas fa-plus-circle"></i> Thêm Loại Phòng
                </button>
            </div>

            <div class="right-tools" style="display: flex; gap: 10px;">
                <a href="?controller=RoomTypeController&action=exportExcel" class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                </a>
                <button class="btn-custom-white" onclick="toggleImport(true)">
                    <i class="fas fa-file-import"></i> Upload File
                </button>
            </div>
        </div>

        <div id="roomTypeForm" class="info-card" style="display: none; margin-top: 20px;">
            <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm Loại Phòng Mới</h4>
            <form id="mainForm" action="?controller=RoomTypeController&action=saveRoomType" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã Loại Phòng</label>
                        <input type="text" name="MaLoaiPhong" id="MaLoaiPhong" class="form-control" placeholder="VD: VIP, STD..." required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tên Loại Phòng</label>
                        <input type="text" name="TenLoaiPhong" id="TenLoaiPhong" class="form-control" placeholder="VD: Phòng Cao Cấp" required>
                    </div>

                    <div class="form-group">
                        <label>Giá Phòng (VNĐ)</label>
                        <input type="number" name="GiaPhong" id="GiaPhong" class="form-control" placeholder="VD: 500000" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Mô Tả Tiện Nghi</label>
                        <input type="text" name="MoTaPhong" id="MoTaPhong" class="form-control" placeholder="VD: Có bồn tắm, view biển...">
                    </div>
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <input type="hidden" name="isEdit" id="isEdit" value="0">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                    <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy bỏ</button>
                </div>
            </form>
        </div>

        <div id="importForm" class="info-card" style="display: none; margin-top: 20px; border: 2px dashed #38bdf8; background: #1e293b;">
            <h4 style="color: #38bdf8; margin-bottom: 15px;">
                <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
            </h4>
            <form action="?controller=RoomTypeController&action=importExcel" method="POST" enctype="multipart/form-data">
                <div style="text-align: center; padding: 30px;">
                    <!-- <p style="color: #ccc; margin-bottom: 10px;">Vui lòng dùng file <b>.xls</b> (Excel 97-2003) để tránh lỗi.</p> -->
                    <input type="file" name="excel_file" required style="margin-bottom: 20px; color: white;">
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
                        <th>Mã Loại</th>
                        <th>Tên Loại Phòng</th>
                        <th>Giá (VNĐ)</th>
                        <th>Mô Tả</th>
                        <th style="text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['roomTypes'])): ?>
                        <?php foreach($data['roomTypes'] as $row): ?>
                        <tr>
                            <td><strong><?= $row['MaLoaiPhong'] ?></strong></td>
                            <td><?= $row['TenLoaiPhong'] ?></td>
                            <td style="color: #38bdf8; font-weight: bold;">
                                <?= number_format($row['GiaPhong'], 0, ',', '.') ?>
                            </td>
                            <td><?= $row['MoTaPhong'] ?></td>
                            <td class="action-buttons" style="text-align: center;">
                                <button class="btn-icon edit" 
                                    onclick="editRoomType('<?= $row['MaLoaiPhong'] ?>', '<?= $row['TenLoaiPhong'] ?>', '<?= $row['GiaPhong'] ?>', '<?= $row['MoTaPhong'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?controller=RoomTypeController&action=deleteRoomType&id=<?= $row['MaLoaiPhong'] ?>" 
                                   class="btn-icon delete" onclick="return confirm('Xóa loại phòng này sẽ ảnh hưởng đến các phòng đang sử dụng nó. Bạn chắc chứ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Chưa có dữ liệu loại phòng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
// --- LOGIC ẨN HIỆN GIỐNG TRANG ROOM VÀ DEPARTMENT ---

function manageDisplay(activeForm) {
    const table = document.querySelector('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('roomTypeForm');
    const importForm = document.getElementById('importForm');

    // 1. Ẩn hết
    if(table) table.style.display = 'none';
    if(toolbar) toolbar.style.display = 'none';
    if(addForm) addForm.style.display = 'none';
    if(importForm) importForm.style.display = 'none';

    // 2. Hiện cái cần thiết
    if (activeForm === 'add') {
        addForm.style.display = 'block';
    } else if (activeForm === 'import') {
        importForm.style.display = 'block';
    } else {
        if(table) table.style.display = 'block';
        if(toolbar) toolbar.style.display = 'flex';
    }
}

function toggleImport(show) { manageDisplay(show ? 'import' : null); }

function toggleForm(show = true) {
    if (show) {
        manageDisplay('add');
        // Reset về chế độ Thêm mới
        document.getElementById('isEdit').value = "0";
        document.getElementById('MaLoaiPhong').readOnly = false;
        document.getElementById('formTitle').innerText = "Thêm Loại Phòng Mới";
        document.getElementById('mainForm').reset();
    } else {
        manageDisplay(null);
    }
}

// Hàm Sửa: Đổ dữ liệu vào Form
function editRoomType(id, name, price, desc) {
    manageDisplay('add'); 
    
    document.getElementById('formTitle').innerText = "Chỉnh sửa loại: " + id;
    document.getElementById('isEdit').value = "1"; // Đánh dấu là Sửa
    
    document.getElementById('MaLoaiPhong').value = id;
    document.getElementById('MaLoaiPhong').readOnly = true; // Khóa mã lại
    
    document.getElementById('TenLoaiPhong').value = name;
    document.getElementById('GiaPhong').value = price;
    document.getElementById('MoTaPhong').value = desc;
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>