
    <main class="main-content">
        <section class="content-body">
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <button class="btn btn-primary" onclick="toggleForm(true)">
                        <i class="fas fa-plus-circle"></i> Thêm mới
                    </button>

                    <form action="?controller=DiscountController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>"
                               placeholder="Tìm theo mã hoặc tên giảm giá..."
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 260px;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="right-tools" style="display: flex; gap: 10px;">
                    <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                    <a href="?controller=DiscountController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                    </a>
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                       <i class="fas fa-file-import"></i> Tải lên file
                    </button>
                </div>
            </div>

            <div id="discountForm" class="info-card" style="display: none; margin-top: 20px;">
                <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm giảm giá mới</h4>
                <form id="mainDiscountForm" action="?controller=DiscountController&action=saveDiscount" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Mã giảm giá</label>
                            <input type="text" name="MaGiamGia" id="MaGiamGia" class="form-control" placeholder="VD: GG01" required>
                        </div>
                        <div class="form-group">
                            <label>Người tạo</label>
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
                            <label>Tên giảm giá</label>
                            <input type="text" name="TenGiamGia" id="TenGiamGia" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tỷ lệ giảm (%)</label>
                            <input type="number" name="TyLeGiamGia" id="TyLeGiamGia" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Mô tả</label>
                            <input type="text" name="MoTaGiamGia" id="MoTaGiamGia" class="form-control">
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <input type="hidden" name="isEdit" id="isEdit" value="0">
                        <button type="submit" class="btn btn-primary">Xác nhận lưu</button>
                        <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy bỏ</button>
                    </div>
                </form>
            </div>

            <div id="importForm" class="info-card" style="display: none; margin-top: 20px; border: 2px dashed #38bdf8; background: #1e293b;">
                <h4 style="color: #38bdf8; margin-bottom: 15px;">
                    <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
                </h4>
                <form action="?controller=DiscountController&action=importExcel" method="POST" enctype="multipart/form-data" style="display:flex; gap: 10px; align-items:center;">
                    <input type="file" name="excel_file" accept=".xlsx,.xls" required style="color:white;">
                    <button type="submit" class="btn btn-primary">Import</button>
                    <button type="button" class="btn btn-danger" onclick="toggleImport(false)">Hủy</button>
                </form>
                <p style="margin-top: 10px; color: #cbd5e1; font-size: 0.9rem;">
                    Cột: MaGiamGia | TenGiamGia | MoTaGiamGia | TyLeGiamGia | MaNhanVien
                </p>
            </div>

            <div class="table-container" style="margin-top: 20px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ma</th>
                            <th>Tên giảm giá</th>
                            <th>Mô tả</th>
                            <th>Tỷ lệ (%)</th>
                            <th>Người tạo</th>
                            <th style="text-align:center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['discounts'])): ?>
                            <?php foreach($data['discounts'] as $row): ?>
                                <tr>
                                    <td><strong><?= $row['MaGiamGia'] ?></strong></td>
                                    <td><?= $row['TenGiamGia'] ?></td>
                                    <td><?= $row['MoTaGiamGia'] ?></td>
                                    <td><?= (int)$row['TyLeGiamGia'] ?>%</td>
                                    <td><?= $row['NguoiTao'] ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-icon edit"
                                                onclick='editDiscount(<?= json_encode($row["MaGiamGia"]) ?>, <?= json_encode($row["TenGiamGia"]) ?>, <?= json_encode($row["MoTaGiamGia"]) ?>, <?= (int)$row["TyLeGiamGia"] ?>, <?= json_encode($row["MaNhanVien"]) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?controller=DiscountController&action=deleteDiscount&id=<?= $row['MaGiamGia'] ?>" class="btn-icon delete"
                                           onclick="return confirm('Xóa giảm giá này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">Chưa có dữ liệu giảm giá.</td></tr>
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
    const addForm = document.getElementById('discountForm');
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
        const formObj = document.getElementById('mainDiscountForm');
        if (formObj) formObj.reset();

        document.getElementById('isEdit').value = "0";
        document.getElementById('formTitle').innerText = "Thêm giảm giá mới";
        document.getElementById('MaGiamGia').readOnly = false;
        document.getElementById('MaGiamGia').value = "";
    } else {
        manageDisplay(null);
    }
}

function editDiscount(id, name, desc, rate, employeeId) {
    manageDisplay('add');

    document.getElementById('formTitle').innerText = "Chỉnh sửa giảm giá: " + id;
    document.getElementById('isEdit').value = "1";

    document.getElementById('MaGiamGia').value = id;
    document.getElementById('MaGiamGia').readOnly = true;
    document.getElementById('TenGiamGia').value = name || "";
    document.getElementById('MoTaGiamGia').value = desc || "";
    document.getElementById('TyLeGiamGia').value = rate;
    document.getElementById('MaNhanVien').value = employeeId;

    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
