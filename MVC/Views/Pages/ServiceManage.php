<main class="main-content">
    <header class="top-header">
        <div class="user-info">
            Chào mừng: <strong>Quản trị viên</strong>
        </div>
    </header>

    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                <button class="btn btn-primary" onclick="showAddForm()">
                    <i class="fas fa-plus-circle"></i> Thêm dịch vụ
                </button>
                
                <form action="?controller=ServiceController&action=index" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" 
                           placeholder="Tìm tên hoặc mô tả..." 
                           style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 300px;">
                    <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="right-tools">
                <button class="btn-custom-white" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>

        <!-- Form thêm/sửa -->
        <div id="serviceForm" class="info-card" style="display: none; margin-top: 20px;">
            <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm dịch vụ mới</h4>
            <form id="mainServiceForm" action="?controller=ServiceController&action=saveService" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã Dịch Vụ *</label>
                        <input type="text" name="MaDichVu" id="MaDichVu" class="form-control" 
                                  placeholder="VD: DV001" required>
                           <small style="color: var(--text-muted);">Nhập mã duy nhất cho dịch vụ</small>
                    </div>
                    <div class="form-group">
                        <label>Tên Dịch Vụ *</label>
                        <input type="text" name="TenDichVu" id="TenDichVu" class="form-control" placeholder="VD: Giặt ủi" required>
                    </div>
                    <div class="form-group">
                        <label>Chi Phí (VNĐ) *</label>
                        <input type="number" name="ChiPhiDichVu" id="ChiPhiDichVu" class="form-control" placeholder="0" required min="0">
                    </div>
                    <div class="form-group" style="grid-column: span 3;">
                        <label>Mô Tả</label>
                        <textarea name="MoTaDichVu" id="MoTaDichVu" class="form-control" rows="3" placeholder="Mô tả chi tiết về dịch vụ..."></textarea>
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <input type="hidden" name="isEdit" id="isEdit" value="0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Xác nhận Lưu
                    </button>
                    <button type="button" class="btn btn-danger" onclick="hideForm()">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng dịch vụ -->
        <div class="table-container" style="margin-top: 20px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã DV</th>
                        <th>Tên Dịch Vụ</th>
                        <th>Mô Tả</th>
                        <th>Chi Phí</th>
                        <th style="text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['services'])): ?>
                        <?php foreach($data['services'] as $row): ?>
                        <tr>
                            <td><strong>#<?= $row['MaDichVu'] ?></strong></td>
                            <td><?= htmlspecialchars($row['TenDichVu']) ?></td>
                            <td><?= htmlspecialchars($row['MoTaDichVu']) ?></td>
                            <td class="salary-text"><?= number_format($row['ChiPhiDichVu']) ?> đ</td>
                            <td class="action-buttons">
                                <button class="btn-icon edit" 
                                        data-id="<?= $row['MaDichVu'] ?>"
                                        data-name="<?= htmlspecialchars($row['TenDichVu']) ?>"
                                        data-desc="<?= htmlspecialchars($row['MoTaDichVu']) ?>"
                                        data-price="<?= $row['ChiPhiDichVu'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete"
                                        data-id="<?= $row['MaDichVu'] ?>"
                                        data-name="<?= htmlspecialchars($row['TenDichVu']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Không tìm thấy dịch vụ nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
// Hiển thị form thêm mới
function showAddForm() {
    const form = document.getElementById('serviceForm');
    const table = document.querySelector('.table-container');
    const toolbar = document.querySelector('.toolbar');
    
    form.style.display = 'block';
    table.style.display = 'none';
    toolbar.style.display = 'none';
    
    // Reset form
    document.getElementById('isEdit').value = "0";
    document.getElementById('MaDichVu').value = "";
    document.getElementById('MaDichVu').placeholder = "Tự động tạo";
    document.getElementById('formTitle').innerText = "Thêm Dịch Vụ Mới";
    document.getElementById('mainServiceForm').reset();
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Ẩn form
function hideForm() {
    const form = document.getElementById('serviceForm');
    const table = document.querySelector('.table-container');
    const toolbar = document.querySelector('.toolbar');
    
    form.style.display = 'none';
    table.style.display = 'block';
    toolbar.style.display = 'flex';
}

// XỬ LÝ NÚT SỬA - Dùng Event Delegation
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút Sửa
    document.querySelectorAll('.btn-icon.edit').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const desc = this.dataset.desc;
            const price = this.dataset.price;
            
            console.log('Edit clicked:', {id, name, desc, price}); // DEBUG
            
            // Hiển thị form
            document.getElementById('serviceForm').style.display = 'block';
            document.querySelector('.table-container').style.display = 'none';
            document.querySelector('.toolbar').style.display = 'none';
            
            // Điền dữ liệu
            document.getElementById('formTitle').innerText = "Chỉnh sửa dịch vụ: " + name;
            document.getElementById('isEdit').value = "1";
            document.getElementById('MaDichVu').value = id;
            document.getElementById('MaDichVu').placeholder = id;
            document.getElementById('TenDichVu').value = name;
            document.getElementById('MoTaDichVu').value = desc;
            document.getElementById('ChiPhiDichVu').value = price;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    // Xử lý nút Xóa
    document.querySelectorAll('.btn-icon.delete').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            console.log('Delete clicked:', {id, name}); // DEBUG
            
            if (!id || id === '') {
                alert('Lỗi: Không tìm thấy mã dịch vụ!');
                return;
            }
            
            if (confirm(`Bạn có chắc chắn muốn xóa dịch vụ "${name}"?\n\nThao tác này không thể hoàn tác!`)) {
                // Hiển thị loading
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
                overlay.innerHTML = '<div style="background:white;padding:30px;border-radius:10px;color:#333;"><i class="fas fa-spinner fa-spin" style="font-size:2rem;color:#3b82f6;"></i><p style="margin-top:10px;color:#333;">Đang xóa dịch vụ...</p></div>';
                document.body.appendChild(overlay);
                
                // Chuyển hướng
                window.location.href = `?controller=ServiceController&action=deleteService&id=${id}`;
            }
        });
    });
});
</script>