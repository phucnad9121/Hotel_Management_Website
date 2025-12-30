

    <main class="main-content">
        
        
<section class="content-body">
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <button class="btn btn-primary" onclick="toggleGuestForm(true)">
                        <i class="fas fa-plus-circle"></i> Thêm khách hàng
                    </button>

                    <form action="?controller=GuestController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" 
                               placeholder="Tìm theo tên, SĐT, CMND..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 300px;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>

                <div class="right-tools" style="display: flex; gap: 10px;">
                     <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                    <a href="?controller=GuestController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                    </a>
                    
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                       <i class="fas fa-file-import" style="color: #e67e22;"></i> Nhập File
                    </button>

                    <button class="btn-custom-white" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Làm mới
                    </button>
                </div>
            </div>

            <div id="guestForm" class="info-card" style="display: none; margin-bottom: 20px; border: 1px solid var(--ocean-blue);">
                <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm khách hàng mới</h4>
                <form id="mainGuestForm" action="?controller=GuestController&action=saveGuest" method="POST">
                    <input type="hidden" name="ma_khach_hang" id="ma_khach_hang">
                    
                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Họ (Đệm):</label>
                            <input type="text" name="ho" id="ho" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tên:</label>
                            <input type="text" name="ten" id="ten" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại (*):</label>
                            <input type="text" name="sdt" id="sdt" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>CMND/CCCD:</label>
                            <input type="text" name="cmnd" id="cmnd" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ:</label>
                            <input type="text" name="diachi" id="diachi" class="form-control">
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                        <button type="button" class="btn btn-danger" onclick="toggleGuestForm(false)">Hủy bỏ</button>
                    </div>
                </form>
            </div>
            <div id="importForm" class="info-card" style="display: none; margin-bottom: 20px; border: 2px dashed #e67e22; background: #1e293b;">
                <h4 style="color: #e67e22; margin-bottom: 15px;">
                    <i class="fas fa-file-import"></i> Nhập khách hàng từ Excel
                </h4>
                <form action="?controller=GuestController&action=importExcel" method="POST" enctype="multipart/form-data">
                    <div style="text-align: center; padding: 20px;">
                    <p style="color: #ccc; margin-bottom: 10px;">
                            Cấu trúc file Excel (6 cột): <br>
                            <strong>Họ | Tên | SĐT | Email | CMND | Địa chỉ</strong>
                        </p>
                        
                        <input type="file" name="excel_file" required style="color: white; margin-bottom: 20px;">
                        
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button type="submit" class="btn btn-primary">Tải lên</button>
                            <button type="button" class="btn btn-danger" onclick="toggleImport(false)">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã KH</th>
                            <th>Họ và tên</th>
                            <th>SĐT</th>
                            <th>CMND/CCCD</th>
                            <th>Trạng thái</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['guests'])): ?>
                            <?php foreach($data['guests'] as $row): ?>
                            <tr>
                                <td><strong>#<?= $row['MaKhachHang'] ?></strong></td>
                                <td><?= $row['HoKhachHang'] ?> <?= $row['TenKhachHang'] ?></td>
                                <td><?= $row['SoDienThoaiKhachHang'] ?></td>
                                <td><?= $row['CMND_CCCDKhachHang'] ?></td>
                                <td>
                                    <?php 
                                    $statusColor = $row['TrangThai'] == 'Reserved' ? '#e74c3c' : '#27ae60';
                                    $statusText = $row['TrangThai'] == 'Reserved' ? 'Đang đặt phòng' : 'Tự do';
                                    ?>
                                    <span class="badge" style="background: <?= $statusColor ?>; color: white; border: none;">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn-icon edit" 
                                        onclick='editGuest(
                                            <?= json_encode($row["MaKhachHang"]) ?>, 
                                            <?= json_encode($row["HoKhachHang"]) ?>, 
                                            <?= json_encode($row["TenKhachHang"]) ?>, 
                                            <?= json_encode($row["SoDienThoaiKhachHang"]) ?>, 
                                            <?= json_encode($row["EmailKhachHang"]) ?>, 
                                            <?= json_encode($row["CMND_CCCDKhachHang"]) ?>, 
                                            <?= json_encode($row["DiaChi"]) ?>
                                        )'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <a href="?controller=GuestController&action=deleteGuest&id=<?= $row['MaKhachHang'] ?>" 
                                       class="btn-icon delete" 
                                       onclick="return confirm('Bạn có chắc muốn xóa khách hàng này? Nếu họ đã từng đặt phòng, hệ thống sẽ chặn thao tác này.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">Không tìm thấy khách hàng nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <script>
        function toggleGuestForm(show) {
            const formDiv = document.getElementById('guestForm');
            const formTitle = document.getElementById('formTitle');
            const mainForm = document.getElementById('mainGuestForm');
            const maInput = document.getElementById('ma_khach_hang');

            if (show) {
                formDiv.style.display = 'block';
                // Mặc định là chế độ Thêm mới -> Reset form
                if (maInput.value === "") {
                    formTitle.innerText = "Thêm Khách Hàng Mới";
                    mainForm.reset();
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                formDiv.style.display = 'none';
                mainForm.reset();
                maInput.value = ""; // Clear ID để lần sau bật lên là Thêm mới
            }
        }

        function editGuest(id, ho, ten, sdt, email, cmnd, diachi) {
            // 1. Đổ dữ liệu vào form
            document.getElementById('ma_khach_hang').value = id;
            document.getElementById('ho').value = ho;
            document.getElementById('ten').value = ten;
            document.getElementById('sdt').value = sdt;
            document.getElementById('email').value = email;
            document.getElementById('cmnd').value = cmnd;
            document.getElementById('diachi').value = diachi;

            // 2. Đổi tiêu đề
            document.getElementById('formTitle').innerText = "Cập Nhật Thông Tin Khách Hàng #" + id;

            // 3. Hiện form
            toggleGuestForm(true);
        }


        function toggleImport(show) {
            const importForm = document.getElementById('importForm');
            const guestForm = document.getElementById('guestForm'); // Form thêm mới
            
            if (show) {
                importForm.style.display = 'block';
                if(guestForm) guestForm.style.display = 'none'; // Ẩn form thêm mới nếu đang mở
            } else {
                importForm.style.display = 'none';
            }
        }
        </script>
    </main>
