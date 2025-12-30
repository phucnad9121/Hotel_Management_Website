<main class="main-content">
    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                <button class="btn btn-primary" onclick="toggleForm(true)">
                    <i class="fas fa-plus-circle"></i> Thêm mới
                </button>
                
                <form action="?controller=EmployeeController&action=index" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword"
                        value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>"
                        placeholder="Tìm tên hoặc mã NV..."
                        style="padding: 8px 15px; border-radius: 8px; border:1px solid var(--border-color);background:#555960ff;color:white;">
                    <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="right-tools" style="display: flex; gap: 10px;">
                <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                <a href="?controller=EmployeeController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color:#16a34a;"></i> Xuất Excel
                </a>
                <button class="btn-custom-white" onclick="toggleImport(true)">
                    <i class="fas fa-file-import"></i> Upload File
                </button>
            </div>
        </div>

        <!-- Form thêm / sửa NV -->
        <div id="employeeForm" class="info-card" style="display: none; margin-top: 20px;">
            <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm nhân viên mới</h4>
            <form id="mainEmpForm" action="?controller=EmployeeController&action=save" method="POST">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Mã Nhân Viên</label>
                        <input type="text" name="MaNhanVien" id="MaNhanVien" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Họ</label>
                        <input type="text" name="HoNhanVien" id="HoNhanVien" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tên</label>
                        <input type="text" name="TenNhanVien" id="TenNhanVien" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="SoDienThoaiNV" id="SoDienThoaiNV" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="EmailNhanVien" id="EmailNhanVien" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Bộ phận</label>
                        <select name="MaBoPhan" id="MaBoPhan" class="form-control" required>
                            <option value="">--Chọn bộ phận--</option>
                            <?php foreach($data['departments'] as $bp): ?>
                                <option value="<?= $bp['MaBoPhan'] ?>"><?= $bp['TenBoPhan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chức danh</label>
                        <input type="text" name="ChucDanhNV" id="ChucDanhNV" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>CMND/CCCD</label>
                        <input type="text" name="CMND_CCCD" id="CMND_CCCD" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày vào làm</label>
                        <input type="date" name="NgayVaoLam" id="NgayVaoLam" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Địa chỉ</label>
                        <input type="text" name="DiaChi" id="DiaChi" class="form-control">
                    </div>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                    <input type="hidden" name="isEdit" id="isEdit" value="0">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy</button>
                </div>
            </form>
        </div>

        <!-- Import Excel -->
        <div id="importForm" class="info-card" style="display:none;margin-top:20px;border:2px dashed #38bdf8;background:#1e293b;">
            <h4 style="color:#38bdf8;margin-bottom:15px;">
                <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
            </h4>
            <form action="?controller=EmployeeController&action=importExcel" method="POST" enctype="multipart/form-data">
                <div style="text-align:center;padding:30px;background:#2d333b;border-radius:8px;">
                    <i class="fas fa-cloud-upload-alt" style="font-size:40px;color:#38bdf8;margin-bottom:10px;"></i>
                    <p style="color:#ccc;margin-bottom:15px;">Chọn file Excel (.xlsx)</p>

                    <input type="file" name="excel_file" required style="margin-bottom:20px;color:white;">
                    <div style="display:flex;gap:10px;justify-content:center;">
                        <button type="submit" class="btn btn-primary">Tải lên</button>
                        <button type="button" class="btn btn-danger" onclick="toggleImport(false)">Hủy</button>
                    </div>
                </div>
            </form>
        </div>

 <!-- Table Employee -->
<div class="employee-table-wrapper">
    <table class="employee-table">
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Họ & Tên</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>CMND/CCCD</th>
                <th>Bộ Phận</th>
                <th>Chức Danh</th>
                <th>Ngày Vào Làm</th>
                <th>Địa Chỉ</th>
                <th style="text-align:center;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data['employees'])): ?>
                <?php foreach($data['employees'] as $row): ?>
                <tr>
                    <td><strong><?= $row['MaNhanVien'] ?></strong></td>
                    <td><?= $row['HoNhanVien']." ".$row['TenNhanVien'] ?></td>
                    <td><span class="email-tag"><?= $row['EmailNhanVien'] ?></span></td>
                    <td><?= $row['SoDienThoaiNV'] ?></td>
                    <td><?= $row['CMND_CCCD'] ?></td>
                    <td><span class="badge"><?= $row['TenBoPhan'] ?></span></td>
                    <td><span class="badge"><?= $row['ChucDanhNV'] ?></span></td>
                    <td><?= $row['NgayVaoLam'] ?></td>
                    <td><?= $row['DiaChi'] ?></td>
                    <td style="text-align:center;">
                        <button class="btn-icon edit" onclick="editEmp(
                            '<?= $row['MaNhanVien']?>',
                            '<?= $row['HoNhanVien']?>',
                            '<?= $row['TenNhanVien']?>',
                            '<?= $row['SoDienThoaiNV']?>',
                            '<?= $row['EmailNhanVien']?>',
                            '<?= $row['MaBoPhan']?>',
                            '<?= $row['ChucDanhNV']?>',
                            '<?= $row['CMND_CCCD']?>',
                            '<?= $row['NgayVaoLam']?>',
                            '<?= $row['DiaChi']?>'
                        )"><i class="fas fa-edit"></i></button>

                        <a href="?controller=EmployeeController&action=delete&id=<?= $row['MaNhanVien']?>"
                        class="btn-icon delete" onclick="return confirm('Xóa nhân viên này?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align:center;">Không có dữ liệu phù hợp</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </section>
</main>

<script>
function manageDisplay(active){
    const tableWrapper=document.querySelector('.employee-table-wrapper'); // ⭐ Thêm
    const toolbar=document.querySelector('.toolbar');
    const form=document.getElementById('employeeForm');
    const importF=document.getElementById('importForm');

    // Ẩn hết trước
    tableWrapper.style.display='none';
    toolbar.style.display='none';
    form.style.display='none';
    importF.style.display='none';

    // Bật đúng thành phần theo yêu cầu
    if(active === 'add') form.style.display='block';
    else if(active === 'edit') form.style.display='block';
    else if(active === 'import') importF.style.display='block';
    else{
        tableWrapper.style.display='block';
        toolbar.style.display='flex';
    }
}

function toggleForm(show){ 
    if(show){
        manageDisplay('add');
        document.getElementById('isEdit').value="0";
        document.getElementById('mainEmpForm').reset();
        document.getElementById('MaNhanVien').readOnly=false;
        document.getElementById('formTitle').innerText="Thêm nhân viên mới";
    }else manageDisplay(null);
}

function toggleImport(show){ manageDisplay(show?'import':null); }

//Chế độ sửa
function editEmp(id,ho,ten,sdt,email,bp,chucdanh,cccd,date,address){
    manageDisplay('add');
    document.getElementById('formTitle').innerText="Chỉnh sửa nhân viên: "+id;
    document.getElementById('isEdit').value="1";
    document.getElementById('MaNhanVien').readOnly=true;

    MaNhanVien.value=id;
    HoNhanVien.value=ho;
    TenNhanVien.value=ten;
    SoDienThoaiNV.value=sdt;
    EmailNhanVien.value=email;
    MaBoPhan.value=bp;
    ChucDanhNV.value=chucdanh;
    CMND_CCCD.value=cccd;
    NgayVaoLam.value=date;
    DiaChi.value=address;

    window.scrollTo({top:0,behavior:'smooth'});
}
</script>
