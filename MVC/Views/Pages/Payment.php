<main class="main-content">
    <section class="content-body">
        <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                <form action="?controller=PaymentController&action=index" method="POST" style="display: flex; gap: 5px;">
                    <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>"
                        placeholder="Tìm theo mã đặt phòng hoặc khách hàng..."
                        style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white; min-width: 300px;">
                    <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="right-tools" style="display: flex; gap: 10px;">
                <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                <a href="?controller=PaymentController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                    <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                </a>
                <button class="btn-custom-white" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Làm mới
                </button>
            </div>
        </div>

        <style>
            .booking-row { cursor: pointer; }
            .booking-row.row-selected { background: rgba(56, 189, 248, 0.25) !important; }
            .payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
            @media (max-width: 900px) { .payment-grid { grid-template-columns: 1fr; } }
        </style>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Khách hàng</th>
                        <th>Phòng</th>
                        <th>Ngày nhận</th>
                        <th>Ngày trả</th>
                        <th>Số đêm</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['bookings'])): ?>
                        <?php
                        $statusColor = [
                            'Pending' => '#f39c12',
                            'Confirmed' => '#3498db',
                            'Checkin' => '#27ae60',
                            'Checkout' => '#95a5a6',
                            'Cancelled' => '#e74c3c'
                        ];
                        ?>
                        <?php foreach ($data['bookings'] as $row): ?>
                            <?php
                            $isPaid = !empty($row['DaThanhToan']) && (int)$row['DaThanhToan'] === 1;
                            $statusText = $isPaid ? 'Đã thanh toán' : ($row['TrangThai'] ?? '');
                            $color = $isPaid ? '#22c55e' : ($statusColor[$row['TrangThai'] ?? ''] ?? '#666');
                            ?>
                            <tr class="booking-row"
                                onclick='selectBooking(this, <?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                <td><strong>#<?= $row['MaDatPhong'] ?></strong></td>
                                <td><?= $row['KhachHang'] ?? '' ?></td>
                                <td><?= $row['SoPhong'] ?? '' ?></td>
                                <td><?= !empty($row['NgayNhanPhong']) ? date('d/m/Y', strtotime($row['NgayNhanPhong'])) : '' ?></td>
                                <td><?= !empty($row['NgayTraPhong']) ? date('d/m/Y', strtotime($row['NgayTraPhong'])) : '' ?></td>
                                <td><?= (int)($row['ThoiGianLuuTru'] ?? 0) ?></td>
                                <td><span style="color: <?= $color ?>; font-weight: bold;"><?= $statusText ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">Chưa có dữ liệu đặt phòng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="paymentDetail" class="info-card" style="margin-top: 20px;">
            <h4 style="color: var(--ocean-blue); margin-bottom: 15px;">CHI TIET THANH TOAN</h4>
            <form id="checkoutForm" action="?controller=PaymentController&action=checkout" method="POST">
                <input type="hidden" name="MaDatPhong" id="MaDatPhong" value="">

                <div class="payment-grid">
                    <div class="form-group">
                        <label>Tiền phòng</label>
                        <input type="text" id="TienPhong" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tiền dịch vụ</label>
                        <input type="text" id="TienDichVu" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Mã giảm giá</label>
                        <select name="MaGiamGia" id="MaGiamGia" class="form-control">
                            <option value="none" data-rate="0">Không giảm giá</option>
                            <?php if (!empty($data['discounts'])): ?>
                                <?php foreach ($data['discounts'] as $d): ?>
                                    <option value="<?= $d['MaGiamGia'] ?>" data-rate="<?= (int)$d['TyLeGiamGia'] ?>">
                                        <?= $d['MaGiamGia'] ?> - <?= $d['TenGiamGia'] ?> (<?= (int)$d['TyLeGiamGia'] ?>%)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số tiền giảm</label>
                        <input type="text" id="TienGiam" class="form-control" readonly style="color:#16a34a; font-weight:bold;">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label style="color:#ef4444; font-weight:bold;">TỔNG CỘNG</label>
                        <input type="text" id="TongCong" class="form-control" readonly
                            style="border:2px solid #ef4444; color:#ef4444; font-weight:bold; font-size:1.2rem;">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Phương thức thanh toán</label>
                        <select name="PhuongThuc" id="PhuongThuc" class="form-control">
                            <option value="Cash">Tien mat</option>
                            <option value="Card">The</option>
                            <option value="Transfer">Chuyen khoan</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button id="serviceBtn" type="button" class="btn btn-primary" style="background:#a855f7;" onclick="openServiceModal()" disabled>
                        <i class="fas fa-eye"></i> Xem dịch vụ
                    </button>
                    <button id="calcBtn" type="button" class="btn btn-primary" style="background:#38bdf8;" onclick="recalculate()" disabled>
                        <i class="fas fa-calculator"></i> Tính lại
                    </button>
                    <button id="checkoutBtn" type="submit" class="btn btn-primary" style="background:#22c55e;" disabled>
                        <i class="fas fa-credit-card"></i> Thanh toán & Trả phòng
                    </button>
                    <button type="button" class="btn btn-outline" onclick="resetPaymentForm()">
                        <i class="fas fa-sync"></i> Làm mới
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<!-- Modal xem dich vu -->
<div id="serviceModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--card-bg); padding: 20px; border-radius: 15px; max-width: 900px; width: 92%; max-height: 85vh; overflow: auto;">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="color: var(--ocean-blue); margin: 0;">
                <i class="fas fa-concierge-bell"></i> Dịch vụ đã dùng - Booking #<span id="serviceBookingId"></span>
            </h3>
            <button class="btn btn-danger" onclick="closeServiceModal()" style="padding: 8px 12px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="table-container" style="margin-top: 10px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tên dịch vụ</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                        <th>Ngày sử dụng</th>
                    </tr>
                </thead>
                <tbody id="serviceTableBody">
                    <tr><td colspan="5" style="text-align:center;">Chưa có dữ liệu.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let selectedBooking = null;

function toInt(val) {
    const n = parseInt(val, 10);
    return Number.isFinite(n) ? n : 0;
}

function formatVND(val) {
    const n = toInt(val);
    return n.toLocaleString('vi-VN') + ' VNĐ';
}

function getSelectedDiscountRate() {
    const sel = document.getElementById('MaGiamGia');
    if (!sel) return 0;
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return 0;
    return toInt(opt.getAttribute('data-rate'));
}

function updateButtons() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    const serviceBtn = document.getElementById('serviceBtn');
    const calcBtn = document.getElementById('calcBtn');

    const hasBooking = !!selectedBooking;
    const isPaid = hasBooking && toInt(selectedBooking.DaThanhToan) === 1;

    if (serviceBtn) serviceBtn.disabled = !hasBooking;
    if (calcBtn) calcBtn.disabled = !hasBooking;
    if (checkoutBtn) checkoutBtn.disabled = !hasBooking || isPaid;
}

function recalculate() {
    if (!selectedBooking) {
        document.getElementById('TienGiam').value = formatVND(0);
        document.getElementById('TongCong').value = formatVND(0);
        return;
    }

    const roomCost = toInt(selectedBooking.SoTienDatPhong);
    const serviceCost = toInt(selectedBooking.TienDichVu);
    const rate = getSelectedDiscountRate();

    const discountAmount = Math.floor((roomCost + serviceCost) * rate / 100);
    const total = Math.max(0, roomCost + serviceCost - discountAmount);

    document.getElementById('TienPhong').value = formatVND(roomCost);
    document.getElementById('TienDichVu').value = formatVND(serviceCost);
    document.getElementById('TienGiam').value = formatVND(discountAmount);
    document.getElementById('TongCong').value = formatVND(total);
}

function selectBooking(rowEl, booking) {
    selectedBooking = booking || null;

    document.querySelectorAll('.booking-row').forEach(r => r.classList.remove('row-selected'));
    if (rowEl) rowEl.classList.add('row-selected');

    document.getElementById('MaDatPhong').value = booking.MaDatPhong || '';
    const sel = document.getElementById('MaGiamGia');
    if (sel) {
        sel.value = booking.MaGiamGia ? booking.MaGiamGia : 'none';
    }

    updateButtons();
    recalculate();

    const target = document.getElementById('paymentDetail');
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetPaymentForm() {
    selectedBooking = null;
    document.querySelectorAll('.booking-row').forEach(r => r.classList.remove('row-selected'));

    const form = document.getElementById('checkoutForm');
    if (form) form.reset();

    document.getElementById('MaDatPhong').value = '';
    document.getElementById('TienPhong').value = '';
    document.getElementById('TienDichVu').value = '';
    document.getElementById('TienGiam').value = '';
    document.getElementById('TongCong').value = '';

    updateButtons();
}

function openServiceModal() {
    if (!selectedBooking || !selectedBooking.MaDatPhong) return;

    const modal = document.getElementById('serviceModal');
    const bookingId = selectedBooking.MaDatPhong;
    document.getElementById('serviceBookingId').textContent = bookingId;

    const tbody = document.getElementById('serviceTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Dang tai du lieu...</td></tr>';

    modal.style.display = 'flex';

    fetch(`?controller=PaymentController&action=getServices&id=${encodeURIComponent(bookingId)}`)
        .then(res => res.json())
        .then(rows => {
            if (!rows || rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Chua su dung dich vu.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            rows.forEach(r => {
                const tr = document.createElement('tr');
                const usedAt = r.NgaySuDung ? new Date(r.NgaySuDung).toLocaleString('vi-VN') : '';
                tr.innerHTML = `
                    <td>${r.TenDichVu || ''}</td>
                    <td>${toInt(r.SoLuong)}</td>
                    <td>${formatVND(r.DonGia)}</td>
                    <td>${formatVND(r.ThanhTien)}</td>
                    <td>${usedAt}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Loi tai du lieu dich vu.</td></tr>';
        });
}

function closeServiceModal() {
    document.getElementById('serviceModal').style.display = 'none';
}

document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) closeServiceModal();
});

document.getElementById('MaGiamGia').addEventListener('change', function() {
    recalculate();
});

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (!selectedBooking || !selectedBooking.MaDatPhong) {
        alert('Vui lòng chọn booking để thanh toán!');
        e.preventDefault();
        return;
    }

    if (toInt(selectedBooking.DaThanhToan) === 1) {
        alert('Booking này đã được thanh toán!');
        e.preventDefault();
        return;
    }

    const ok = confirm(`Xác nhận thanh toán & trả phòng cho Booking #${selectedBooking.MaDatPhong}?`);
    if (!ok) e.preventDefault();
});

updateButtons();
</script>
