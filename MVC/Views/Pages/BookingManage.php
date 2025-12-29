
    <main class="main-content">
        <header class="top-header">
            <div class="user-info">
                Chào mừng: <strong>Quản trị viên</strong>
            </div>
        </header>
        
        <section class="content-body">
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <form action="?controller=BookingController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" 
                               placeholder="Tìm theo tên, SĐT, mã booking..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 300px;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>

                <div class="right-tools">
                    <button class="btn-custom-white" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Làm mới
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã ĐP</th>
                            <th>Khách hàng</th>
                            <th>SĐT</th>
                            <th>Ngày nhận</th>
                            <th>Ngày trả</th>
                            <th>Số đêm</th>
                            <th>Tiền phòng</th>
                            <th>Trạng thái</th>
                            <th>Phòng gán</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['bookings'])): ?>
                            <?php 
                            // 1. GỌI FILE MODEL VÀO ĐỂ KHÔNG BỊ LỖI "CLASS NOT FOUND"
                            require_once "./MVC/Models/BookingModel.php";
                            $bookingModel = new BookingModel();
                            
                            // 2. XÓA DÒNG "new RoomModel()" VÌ KHÔNG DÙNG ĐẾN
                            
                            foreach($data['bookings'] as $row): 
                                // Xử lý logic như cũ
                                $maLoaiPhong = $bookingModel->parseRoomType($row['GhiChu']);
                                
                                // Lấy phòng đã gán (DB query)
                                $assignedRooms = $bookingModel->getAssignedRooms($row['MaDatPhong']);
                                $roomNumbers = array_map(function($r) { return $r['SoPhong']; }, $assignedRooms);
                                $roomDisplay = empty($roomNumbers) ? 'Chưa gán' : implode(', ', $roomNumbers);
                                
                                // Màu sắc trạng thái
                                $statusColor = [
                                    'Pending' => '#f39c12',
                                    'Confirmed' => '#3498db',
                                    'Checkin' => '#27ae60',
                                    'Checkout' => '#95a5a6',
                                    'Cancelled' => '#e74c3c'
                                ];
                                $color = $statusColor[$row['TrangThai']] ?? '#666';
                            ?>
                            <tr>
                                <td><strong>#<?= $row['MaDatPhong'] ?></strong></td>
                                
                                <td>
                                    <?= (isset($row['HoKhachHang']) ? $row['HoKhachHang'] . ' ' : '') . $row['TenKhachHang'] ?>
                                </td>
                                
                                <td><?= $row['SoDienThoaiKhachHang'] ?></td>
                                <td><?= date('d/m/Y', strtotime($row['NgayNhanPhong'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['NgayTraPhong'])) ?></td>
                                <td><?= $row['ThoiGianLuuTru'] ?> đêm</td>
                                <td class="salary-text"><?= number_format($row['SoTienDatPhong']) ?> đ</td>
                                <td>
                                    <span class="badge" style="background: <?= $color ?>; color: white; border: none;">
                                        <?= $row['TrangThai'] ?>
                                    </span>
                                </td>
                                <td><?= $roomDisplay ?></td>
                                
                                <td class="action-buttons">
                                    <?php if($row['TrangThai'] == 'Pending'): ?>
                                        <button class="btn-icon" style="background: #3498db;" 
                                                onclick="confirmBooking(<?= $row['MaDatPhong'] ?>, '<?= $maLoaiPhong ?>')"
                                                title="Xác nhận & Gán phòng">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($row['TrangThai'] == 'Confirmed'): ?>
                                        <a href="?controller=BookingController&action=checkin&id=<?= $row['MaDatPhong'] ?>" 
                                        class="btn-icon" style="background: #27ae60;"
                                        onclick="return confirm('Xác nhận check-in cho khách?')"
                                        title="Check-in">
                                            <i class="fas fa-door-open"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($row['TrangThai'] == 'Checkin' || $row['TrangThai'] == 'Confirmed'): ?>
                                        <button class="btn-icon" style="background: #e67e22;" 
                                                onclick="addService(<?= $row['MaDatPhong'] ?>)"
                                                title="Thêm dịch vụ">
                                            <i class="fas fa-concierge-bell"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($row['TrangThai'] != 'Checkout' && $row['TrangThai'] != 'Cancelled'): ?>
                                        <a href="?controller=BookingController&action=cancel&id=<?= $row['MaDatPhong'] ?>" 
                                        class="btn-icon delete"
                                        onclick="return confirm('Xác nhận hủy đặt phòng này?')"
                                        title="Hủy đặt phòng">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <button class="btn-icon" style="background: #9b59b6;" 
                                            onclick="viewDetails(<?= $row['MaDatPhong'] ?>)"
                                            title="Xem chi tiết">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" style="text-align:center;">Không tìm thấy booking nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>


<!-- Modal gán phòng -->
<div id="assignRoomModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--card-bg); padding: 30px; border-radius: 15px; max-width: 500px; width: 90%;">
        <h3 style="color: var(--ocean-blue); margin-bottom: 20px;">
            <i class="fas fa-door-open"></i> Gán phòng cho Booking #<span id="bookingId"></span>
        </h3>
        <form method="POST" action="?controller=BookingController&action=confirm">
            <input type="hidden" name="ma_dat_phong" id="modalBookingId">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: white; font-weight: bold;">
                    Chọn phòng trống:
                </label>
                <select name="ma_phong" id="roomSelect" class="form-control" required style="width: 100%; padding: 12px; border-radius: 8px;">
                    <option value="">-- Đang tải danh sách phòng --</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-check"></i> Xác nhận
                </button>
                <button type="button" class="btn btn-danger" onclick="closeModal()" style="flex: 1;">
                    <i class="fas fa-times"></i> Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmBooking(maDatPhong, maLoaiPhong) {
    // Hiển thị modal
    const modal = document.getElementById('assignRoomModal');
    modal.style.display = 'flex';
    
    document.getElementById('bookingId').textContent = maDatPhong;
    document.getElementById('modalBookingId').value = maDatPhong;
    
    // Load danh sách phòng trống
    fetch(`?controller=BookingController&action=getAvailableRooms&type=${maLoaiPhong}`)
        .then(response => response.json())
        .then(rooms => {
            const select = document.getElementById('roomSelect');
            select.innerHTML = '<option value="">-- Chọn phòng --</option>';
            
            if(rooms.length === 0) {
                select.innerHTML = '<option value="">Không còn phòng trống!</option>';
                return;
            }
            
            rooms.forEach(room => {
                const option = document.createElement('option');
                option.value = room.MaPhong;
                option.textContent = `Phòng ${room.SoPhong}`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            alert('Lỗi khi tải danh sách phòng!');
            console.error(error);
        });
}

function closeModal() {
    document.getElementById('assignRoomModal').style.display = 'none';
}

function addService(maDatPhong) {
    alert('Chức năng thêm dịch vụ đang được phát triển!\nMã đặt phòng: ' + maDatPhong);
}

function viewDetails(maDatPhong) {
    alert('Chức năng xem chi tiết đang được phát triển!\nMã đặt phòng: ' + maDatPhong);
}

// Đóng modal khi click bên ngoài
document.getElementById('assignRoomModal').addEventListener('click', function(e) {
    if(e.target === this) {
        closeModal();
    }
});
</script>