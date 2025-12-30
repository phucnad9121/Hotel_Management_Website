<main class="main-content">
    <header class="top-header">
        <div class="user-info">
            Chào mừng: <strong>Quản trị viên</strong>
        </div>
    </header>

    <section class="content-body">
        <!-- Toolbar -->
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div class="left-tools">
                <h2 style="color: var(--ocean-blue); margin: 0;">
                    <i class="fas fa-chart-line"></i> BÁO CÁO & THỐNG KÊ
                </h2>
            </div>
            <div class="right-tools">
                <a href="?controller=ReportController&action=exportReport&from_date=<?= $data['fromDate'] ?>&to_date=<?= $data['toDate'] ?>" 
                   class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                </a>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="info-card" style="margin-bottom: 25px;">
            <form action="?controller=ReportController&action=index" method="POST" style="display: flex; gap: 15px; align-items: end;">
                <div class="form-group" style="flex: 1;">
                    <label style="color: var(--text-white); margin-bottom: 5px; display: block;">Từ ngày:</label>
                    <input type="date" name="from_date" value="<?= $data['fromDate'] ?>" class="form-control">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label style="color: var(--text-white); margin-bottom: 5px; display: block;">Đến ngày:</label>
                    <input type="date" name="to_date" value="<?= $data['toDate'] ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary" style="height: fit-content;">
                    <i class="fas fa-filter"></i> Lọc dữ liệu
                </button>
            </form>
        </div>

        <!-- Thống kê tổng quan -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="info-card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin: 0;">Tổng Booking</p>
                        <h2 style="margin: 10px 0 0 0; font-size: 2.5rem;"><?= $data['stats']['totalBookings'] ?></h2>
                    </div>
                    <i class="fas fa-calendar-check" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>

            <div class="info-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin: 0;">Doanh thu phòng</p>
                        <h2 style="margin: 10px 0 0 0; font-size: 1.8rem;"><?= number_format($data['stats']['totalRoomRevenue']) ?> đ</h2>
                    </div>
                    <i class="fas fa-bed" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>

            <div class="info-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin: 0;">Doanh thu dịch vụ</p>
                        <h2 style="margin: 10px 0 0 0; font-size: 1.8rem;"><?= number_format($data['stats']['totalServiceRevenue']) ?> đ</h2>
                    </div>
                    <i class="fas fa-concierge-bell" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>

            <div class="info-card" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin: 0;">Tổng doanh thu</p>
                        <h2 style="margin: 10px 0 0 0; font-size: 1.8rem;"><?= number_format($data['stats']['totalRevenue']) ?> đ</h2>
                    </div>
                    <i class="fas fa-coins" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Biểu đồ trạng thái -->
        <div class="info-card" style="margin-bottom: 25px;">
            <h3 style="color: var(--ocean-blue); margin-bottom: 15px;">
                <i class="fas fa-chart-pie"></i> Thống kê theo trạng thái
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                <?php 
                $statusColors = [
                    'Pending' => '#f39c12',
                    'Confirmed' => '#3498db',
                    'Checkin' => '#27ae60',
                    'Checkout' => '#95a5a6',
                    'Cancelled' => '#e74c3c'
                ];
                $statusLabels = [
                    'Pending' => 'Chờ xác nhận',
                    'Confirmed' => 'Đã xác nhận',
                    'Checkin' => 'Đã nhận phòng',
                    'Checkout' => 'Đã trả phòng',
                    'Cancelled' => 'Đã hủy'
                ];
                foreach($data['stats']['statusBreakdown'] as $status => $count): 
                ?>
                <div style="background: <?= $statusColors[$status] ?>; color: white; padding: 15px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold;"><?= $count ?></div>
                    <div style="opacity: 0.9; font-size: 0.9rem;"><?= $statusLabels[$status] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bảng chi tiết -->
        <div class="table-container">
            <h3 style="color: var(--ocean-blue); margin-bottom: 15px;">
                <i class="fas fa-list"></i> Chi tiết đặt phòng
            </h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã ĐP</th>
                        <th>Khách hàng</th>
                        <th>Phòng</th>
                        <th>Ngày nhận</th>
                        <th>Ngày trả</th>
                        <th>Tiền phòng</th>
                        <th>Tiền DV</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['bookings'])): ?>
                        <?php foreach($data['bookings'] as $row): 
                            $statusColors = [
                                'Pending' => '#f39c12',
                                'Confirmed' => '#3498db',
                                'Checkin' => '#27ae60',
                                'Checkout' => '#95a5a6',
                                'Cancelled' => '#e74c3c'
                            ];
                            $color = $statusColors[$row['TrangThai']] ?? '#666';
                        ?>
                        <tr>
                            <td><strong>#<?= $row['MaDatPhong'] ?></strong></td>
                            <td>
                                <a href="#" onclick="viewCustomerServices(<?= $row['MaKhachHang'] ?>); return false;" style="color:inherit; text-decoration:underline;">
                                    <?= $row['TenKhachHang'] ?>
                                </a>
                                <br>
                                <small style="color: var(--text-muted);"><?= $row['SoDienThoaiKhachHang'] ?></small>
                            </td>
                            <td><?= $row['SoPhong'] ?? '<span style="color: #ef4444;">Chưa gán</span>' ?></td>
                            <td><?= date('d/m/Y', strtotime($row['NgayNhanPhong'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['NgayTraPhong'])) ?></td>
                            <td class="salary-text"><?= number_format($row['SoTienDatPhong']) ?> đ</td>
                            <td class="salary-text"><?= number_format($row['TongTienDichVu'] ?? 0) ?> đ</td>
                            <td class="salary-text" style="color: #10b981; font-weight: bold;"><?= number_format($row['TongTien']) ?> đ</td>
                            <td>
                                <span class="badge" style="background: <?= $color ?>; color: white;">
                                    <?= $row['TrangThai'] ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button class="btn-icon" style="background: #9b59b6;" onclick='viewBookingDetail(<?= json_encode($row["MaDatPhong"], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" style="text-align:center;">Không có dữ liệu trong khoảng thời gian này.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot style="background: var(--card-bg); font-weight: bold;">
                    <tr>
                        <td colspan="5" style="text-align: right;">TỔNG CỘNG:</td>
                        <td class="salary-text"><?= number_format($data['stats']['totalRoomRevenue']) ?> đ</td>
                        <td class="salary-text"><?= number_format($data['stats']['totalServiceRevenue']) ?> đ</td>
                        <td class="salary-text" style="color: #10b981;"><?= number_format($data['stats']['totalRevenue']) ?> đ</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bảng dịch vụ đã sử dụng -->
        <div class="table-container" style="margin-top:30px;">
            <h3 style="color: var(--ocean-blue); margin-bottom: 15px;">
                <i class="fas fa-concierge-bell"></i> Chi tiết dịch vụ đã sử dụng
            </h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã ĐP</th>
                        <th>Tên dịch vụ</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                        <th>Ngày sử dụng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['servicesUsed'])): ?>
                        <?php foreach($data['servicesUsed'] as $svc): ?>
                            <tr>
                                <td><strong>#<?= $svc['MaDatPhong'] ?></strong></td>
                                <td><?= $svc['TenDichVu'] ?></td>
                                <td style="text-align:right;"><?= $svc['SoLuong'] ?? 1 ?></td>
                                <td style="text-align:right;"><?= number_format($svc['DonGia']) ?> đ</td>
                                <td style="text-align:right; color: #10b981; font-weight: bold;"><?= number_format($svc['ThanhTien']) ?> đ</td>
                                <td style="text-align:center;"><?= date('d/m/Y', strtotime($svc['NgaySuDung'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">Không có dịch vụ nào trong khoảng thời gian này.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- Modal xem chi tiết -->
<div id="detailModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto;">
    <div style="background: var(--card-bg); padding: 30px; border-radius: 15px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="detailTitle" style="color: var(--ocean-blue); margin: 0;">
                <i class="fas fa-info-circle"></i> Chi tiết Booking
            </h3>
            <button onclick="closeDetailModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="detailContent" style="color: white;">
            <p style="text-align: center; padding: 40px;">Đang tải...</p>
        </div>
    </div>
</div>

<script>
function viewBookingDetail(id) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    
    modal.style.display = 'flex';
    content.innerHTML = '<p style="text-align: center; padding: 40px;">Đang tải...</p>';
    
    // Fetch chi tiết từ server
    fetch(`?controller=ReportController&action=getBookingDetail&id=${id}`)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<p style="text-align: center; color: #ef4444;">Lỗi tải dữ liệu!</p>';
        });
}

function closeDetailModal() {
    document.getElementById('detailModal').style.display = 'none';
}

// Đóng modal khi click bên ngoài
document.getElementById('detailModal').addEventListener('click', function(e) {
    if(e.target === this) {
        closeDetailModal();
    }
});

// Hiển thị dịch vụ khách hàng đã dùng (từ danh sách khách)
function viewCustomerServices(customerId) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('detailContent');
    const title = document.getElementById('detailTitle');

    modal.style.display = 'flex';
    title.innerHTML = '<i class="fas fa-concierge-bell"></i> Dịch vụ đã sử dụng';
    content.innerHTML = '<p style="text-align: center; padding: 40px;">Đang tải...</p>';

    // lấy ngày lọc từ biến PHP (nếu có)
    const fromDate = '<?= $data['fromDate'] ?>';
    const toDate = '<?= $data['toDate'] ?>';

    fetch(`?controller=ReportController&action=getCustomerServices&id=${customerId}&from_date=${fromDate}&to_date=${toDate}`)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            content.innerHTML = '<p style="text-align:center;color:#ef4444;">Lỗi tải dữ liệu</p>';
        });
}
</script>
