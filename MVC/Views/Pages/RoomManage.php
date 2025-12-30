<main class="main-content">
    <header class="top-header">
        <div class="user-info">
            Chào mừng: <strong>Quản trị viên (Quản lý Phòng)</strong>
        </div>
    </header>

    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                <button class="btn btn-primary" onclick="toggleForm(true)">
                    <i class="fas fa-plus-circle"></i> Thêm phòng mới
                </button>
                
                <form action="?controller=RoomController&action=index" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" placeholder="Tìm tên hoặc mã phòng..." 
                           style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white;">
                    <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="right-tools" style="display: flex; gap: 10px;">
                <a href="?controller=RoomController&action=exportExcel" class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                </a>
                <button class="btn-custom-white" onclick="toggleImport(true)">
                    <i class="fas fa-file-import"></i> Upload File
                </button>
            </div>
        </div>

        <div id="roomForm" class="info-card" style="display: none; margin-top: 20px;">
            <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm phòng mới</h4>
            <form id="mainRoomForm" action="?controller=RoomController&action=saveRoom" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã Phòng (ID)</label>
                        <input type="text" name="MaPhong" id="MaPhong" class="form-control" placeholder="VD: P101" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Số Phòng (Tên hiển thị)</label>
                        <input type="text" name="SoPhong" id="SoPhong" class="form-control" placeholder="VD: Phòng 101" required>
                    </div>

                    <div class="form-group">
                        <label>Loại Phòng</label>
                        <select name="MaLoaiPhong" id="MaLoaiPhong" class="form-control">
                            <?php if(isset($data['roomTypes'])): ?>
                                <?php foreach($data['roomTypes'] as $type): ?>
                                    <option value="<?= $type['MaLoaiPhong'] ?>"><?= $type['TenLoaiPhong'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="KhaDung" id="KhaDung" class="form-control">
                            <option value="Yes">Sẵn sàng (Yes)</option>
                            <option value="No">Bảo trì (No)</option>
                        </select>
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
            <form action="?controller=RoomController&action=importExcel" method="POST" enctype="multipart/form-data">
                <div style="text-align: center; padding: 30px;">
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
                        <th>Mã Phòng</th>
                        <th>Số Phòng</th>
                        <th>Loại Phòng</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['rooms'])): ?>
                        <?php foreach($data['rooms'] as $row): ?>
                        <tr>
                            <td><strong><?= $row['MaPhong'] ?></strong></td>
                            <td><?= $row['SoPhong'] ?></td>
                            <td><?= $row['TenLoaiPhong'] ?></td> <td>
                                <?php if($row['KhaDung'] == 'Yes'): ?>
                                    <span style="color: green; font-weight: bold;">Sẵn sàng</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">Bảo trì</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons" style="text-align: center;">
                                <button class="btn-icon edit" 
                                    onclick="editRoom('<?= $row['MaPhong'] ?>','<?= $row['SoPhong'] ?>','<?= $row['MaLoaiPhong'] ?>','<?= $row['KhaDung'] ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?controller=RoomController&action=deleteRoom&id=<?= $row['MaPhong'] ?>" 
                                   class="btn-icon delete" onclick="return confirm('Xóa phòng này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Chưa có dữ liệu phòng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
// --- LOGIC JS GIỐNG HỆT TRANG DEPARTMENT ---

function manageDisplay(activeForm) {
    const tables = document.querySelectorAll('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('roomForm');
    const importForm = document.getElementById('importForm');

    // Ẩn tất cả
    tables.forEach(t => t.style.display = 'none');
    if(toolbar) toolbar.style.display = 'none';
    if(addForm) addForm.style.display = 'none';
    if(importForm) importForm.style.display = 'none';

    // Hiện cái cần thiết
    if (activeForm === 'add') {
        addForm.style.display = 'block';
    } else if (activeForm === 'import') {
        importForm.style.display = 'block';
    } else {
        tables.forEach(t => t.style.display = 'block');
        if(toolbar) toolbar.style.display = 'flex';
    }
}

function toggleImport(show) { manageDisplay(show ? 'import' : null); }

function toggleForm(show = true) {
    if (show) {
        manageDisplay('add');
        document.getElementById('isEdit').value = "0";
        document.getElementById('MaPhong').readOnly = false; // Mở khóa mã khi thêm mới
        document.getElementById('formTitle').innerText = "Thêm Phòng Mới";
        document.getElementById('mainRoomForm').reset();
    } else {
        manageDisplay(null);
    }
}

// Hàm Sửa: Đổ dữ liệu vào Form
function editRoom(id, soPhong, maLoai, khaDung) {
    manageDisplay('add'); 
    
    document.getElementById('formTitle').innerText = "Chỉnh sửa phòng: " + id;
    document.getElementById('isEdit').value = "1"; // Đánh dấu là Sửa
    
    document.getElementById('MaPhong').value = id;
    document.getElementById('MaPhong').readOnly = true; // Khóa mã lại không cho sửa
    
    document.getElementById('SoPhong').value = soPhong;
    document.getElementById('MaLoaiPhong').value = maLoai; // Combobox tự chọn đúng loại
    document.getElementById('KhaDung').value = khaDung; // Combobox tự chọn Yes/No
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>