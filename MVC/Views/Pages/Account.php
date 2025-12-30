<main class="main-content">
    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                <button class="btn btn-primary" onclick="toggleForm(true)">
                    <i class="fas fa-plus-circle"></i> Thêm mới
                </button>
                
                <form action="?controller=AccountController&action=index" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword"
                        value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>"
                        placeholder="Tìm theo mã hoặc tên đăng nhập..."
                        style="padding: 8px 15px; border-radius: 8px; border:1px solid var(--border-color);background:#555960ff;color:white;min-width:260px;">
                    <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="right-tools" style="display: flex; gap: 10px;">
                <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                <a href="?controller=AccountController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color:#16a34a;"></i> Xuất Excel
                </a>
                <button class="btn-custom-white" onclick="toggleImport(true)">
                    <i class="fas fa-file-import"></i> Tải lên file
                </button>
                <button class="btn-custom-white" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- Form them / sua tai khoan -->
        <div id="accountForm" class="info-card" style="display: none; margin-top: 20px;">
            <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm tài khoản mới</h4>
            <form id="mainAccountForm" action="?controller=AccountController&action=saveAccount" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã đăng nhập</label>
                        <input type="text" name="MaDangNhap" id="MaDangNhap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nhân viên</label>
                        <select name="MaNhanVien" id="MaNhanVien" class="form-control" required>
                            <?php if(!empty($data['employees'])): ?>
                                <?php foreach($data['employees'] as $emp): ?>
                                    <option value="<?= $emp['MaNhanVien'] ?>">
                                        <?= $emp['MaNhanVien'] ?> - <?= $emp['HoNhanVien'] . ' ' . $emp['TenNhanVien'] ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tên đăng nhập</label>
                        <input type="text" name="TenDangNhap" id="TenDangNhap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="MatKhau" id="MatKhau" class="form-control" placeholder="Nhập mật khẩu (bỏ trống nếu không đổi)">
                    </div>
                    <div class="form-group">
                        <label>Người dùng mới</label>
                        <select name="NguoiDungMoi" id="NguoiDungMoi" class="form-control">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <input type="hidden" name="isEdit" id="isEdit" value="0">
                    <button type="submit" class="btn btn-primary">Xác nhận lưu</button>
                    <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy</button>
                </div>
            </form>
        </div>

        <!-- Import Excel -->
        <div id="importForm" class="info-card" style="display: none; margin-top: 20px; border: 2px dashed #38bdf8; background: #1e293b;">
            <h4 style="color: #38bdf8; margin-bottom: 15px;">
                <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
            </h4>
            <form action="?controller=AccountController&action=importExcel" method="POST" enctype="multipart/form-data" style="display:flex; gap: 10px; align-items:center;">
                <input type="file" name="excel_file" accept=".xlsx,.xls" required style="color:white;">
                <button type="submit" class="btn btn-primary">Import</button>
                <button type="button" class="btn btn-danger" onclick="toggleImport(false)">Hủy</button>
            </form>
            <p style="margin-top: 10px; color: #cbd5e1; font-size: 0.9rem;">
                Cột: MaDangNhap | TenDangNhap | MatKhau | MaNhanVien | NguoiDungMoi
            </p>
        </div>

        <!-- Bang tai khoan -->
        <div class="table-container" style="margin-top: 20px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đăng nhập</th>
                        <th>Tên đăng nhập</th>
                        <th>Nhân viên</th>
                        <th>Người dùng mới</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['accounts'])): ?>
                        <?php foreach($data['accounts'] as $row): ?>
                        <tr>
                            <td><strong><?= $row['MaDangNhap'] ?></strong></td>
                            <td><?= $row['TenDangNhap'] ?></td>
                            <td><?= $row['NhanVien'] ?></td>
                            <td><?= $row['NguoiDungMoi'] ?></td>
                            <td class="action-buttons">
                                <button class="btn-icon edit"
                                        onclick='editAccount(<?= json_encode($row["MaDangNhap"]) ?>, <?= json_encode($row["MaNhanVien"]) ?>, <?= json_encode($row["TenDangNhap"]) ?>, <?= json_encode($row["NguoiDungMoi"]) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?controller=AccountController&action=deleteAccount&id=<?= $row['MaDangNhap'] ?>" class="btn-icon delete"
                                   onclick="return confirm('Xóa tài khoản này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Chưa có dữ liệu tài khoản.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
function manageDisplay(activeForm) {
    const tables = document.querySelectorAll('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('accountForm');
    const importForm = document.getElementById('importForm');

    tables.forEach(t => t.style.display = 'none');
    if (toolbar) toolbar.style.display = 'none';
    if (addForm) addForm.style.display = 'none';
    if (importForm) importForm.style.display = 'none';

    if (activeForm === 'add') {
        addForm.style.display = 'block';
    } else if (activeForm === 'import') {
        importForm.style.display = 'block';
    } else {
        tables.forEach(t => t.style.display = 'block');
        if (toolbar) toolbar.style.display = 'flex';
    }
}

function toggleImport(show) {
    manageDisplay(show ? 'import' : null);
}

function toggleForm(show = true) {
    if (show) {
        manageDisplay('add');
        const formObj = document.getElementById('mainAccountForm');
        if (formObj) formObj.reset();

        document.getElementById('isEdit').value = "0";
        document.getElementById('formTitle').innerText = "Thêm tài khoản mới";
        document.getElementById('MaDangNhap').readOnly = false;
        document.getElementById('MaDangNhap').value = "";
        document.getElementById('MatKhau').placeholder = "Nhập mật khẩu";
    } else {
        manageDisplay(null);
    }
}

function editAccount(id, employeeId, username, isNew) {
    manageDisplay('add');
    document.getElementById('formTitle').innerText = "Chỉnh sửa tài khoản: " + id;
    document.getElementById('isEdit').value = "1";

    document.getElementById('MaDangNhap').value = id;
    document.getElementById('MaDangNhap').readOnly = true;
    document.getElementById('MaNhanVien').value = employeeId;
    document.getElementById('TenDangNhap').value = username || "";
    document.getElementById('NguoiDungMoi').value = isNew || "Yes";
    document.getElementById('MatKhau').value = "";
    document.getElementById('MatKhau').placeholder = "Bỏ trống nếu không đổi";

    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
