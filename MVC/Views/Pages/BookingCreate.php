<style>
.booking-wrapper {
    min-height: 100vh;
    background: var(--bg-dark);
    padding: 40px 20px;
}
.booking-container {
    max-width: 900px;
    margin: 0 auto;
    background: var(--card-bg);
    border-radius: 15px;
    padding: 30px;
    color: var(--text-white);
}
.booking-header {
    text-align: center;
    margin-bottom: 30px;
    color: var(--ocean-blue);
}
.form-section {
    background: rgba(255,255,255,0.03);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.form-section h3 {
    color: var(--ocean-blue);
    margin-bottom: 15px;
    border-bottom: 2px solid var(--ocean-blue);
    padding-bottom: 10px;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 15px;
}
.form-row.full {
    grid-template-columns: 1fr;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
.form-control-booking {
    width: 100%;
    padding: 12px;
    background: var(--input-bg);
    border: 1px solid transparent;
    border-radius: 8px;
    color: white;
    font-size: 1rem;
}
.form-control-booking:focus {
    border-color: var(--ocean-blue);
    outline: none;
}
.estimate-box {
    background: linear-gradient(135deg, var(--ocean-blue), #0369a1);
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    color: white;
}
.estimate-amount {
    font-size: 2rem;
    font-weight: bold;
    margin: 10px 0;
}
</style>

<div class="booking-wrapper">
    <div class="booking-container">
        <div class="booking-header">
            <h1><i class="fas fa-calendar-check"></i> ĐẶT PHÒNG KHÁCH SẠN</h1>
            <p>Vui lòng điền đầy đủ thông tin để đặt phòng</p>
        </div>

        <form action="?controller=BookingController&action=handleCreate" method="POST" id="bookingForm">
            <!-- Thông tin khách hàng -->
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Thông tin khách hàng</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên:</label>
                        <input type="text" class="form-control-booking" 
                               value="<?= $data['guest']['HoKhachHang'] ?> <?= $data['guest']['TenKhachHang'] ?>" 
                               readonly>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại:</label>
                        <input type="text" class="form-control-booking" 
                               value="<?= $data['guest']['SoDienThoaiKhachHang'] ?>" 
                               readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="text" class="form-control-booking" 
                               value="<?= $data['guest']['EmailKhachHang'] ?>" 
                               readonly>
                    </div>
                    <div class="form-group">
                        <label>CMND/CCCD:</label>
                        <input type="text" class="form-control-booking" 
                               value="<?= $data['guest']['CMND_CCCDKhachHang'] ?>" 
                               readonly>
                    </div>
                </div>
            </div>

            <!-- Thông tin đặt phòng -->
            <div class="form-section">
                <h3><i class="fas fa-bed"></i> Thông tin đặt phòng</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Loại phòng: *</label>
                        <select name="ma_loai_phong" id="roomType" class="form-control-booking" required onchange="updateEstimate()">
                            <option value="">-- Chọn loại phòng --</option>
                            <?php foreach($data['roomTypes'] as $room): ?>
                                <?php 
                                $selected = (isset($_GET['type']) && $_GET['type'] == $room['MaLoaiPhong']) ? 'selected' : '';
                                ?>
                                <option value="<?= $room['MaLoaiPhong'] ?>" 
                                        data-price="<?= $room['GiaPhong'] ?>"
                                        data-available="<?= $room['SoPhongTrong'] ?>"
                                        <?= $selected ?>>
                                    <?= $room['TenLoaiPhong'] ?> - <?= number_format($room['GiaPhong']) ?> VNĐ/đêm (Còn <?= $room['SoPhongTrong'] ?> phòng)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số phòng còn trống:</label>
                        <input type="text" id="availableRooms" class="form-control-booking" 
                               value="Vui lòng chọn loại phòng" readonly 
                               style="background: #334155; color: #10b981; font-weight: bold;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày nhận phòng: *</label>
                        <input type="date" name="ngay_nhan" id="checkinDate" 
                               class="form-control-booking" required 
                               min="<?= date('Y-m-d') ?>"
                               onchange="updateEstimate()">
                    </div>
                    <div class="form-group">
                        <label>Ngày trả phòng: *</label>
                        <input type="date" name="ngay_tra" id="checkoutDate" 
                               class="form-control-booking" required 
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               onchange="updateEstimate()">
                    </div>
                </div>
                <div class="form-row full">
                    <div class="form-group">
                        <label>Ghi chú:</label>
                        <textarea name="ghi_chu" class="form-control-booking" 
                                  rows="3" placeholder="Yêu cầu đặc biệt (nếu có)..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Ước tính chi phí -->
            <div class="estimate-box">
                <div style="font-size: 1.2rem; margin-bottom: 10px;">
                    <i class="fas fa-calculator"></i> Ước tính chi phí
                </div>
                <div id="estimateDetails" style="margin-bottom: 10px; opacity: 0.9;">
                    Chọn ngày để xem ước tính
                </div>
                <div class="estimate-amount" id="estimateAmount">0 VNĐ</div>
                <div style="font-size: 0.9rem; opacity: 0.8;">
                    * Giá chưa bao gồm dịch vụ phát sinh
                </div>
            </div>

            <!-- Nút hành động -->
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-submit" style="flex: 1;">
                    <i class="fas fa-paper-plane"></i> Gửi yêu cầu đặt phòng
                </button>
                <a href="?controller=GuestController&action=home" class="btn-back" 
                   style="flex: 1; text-align: center; padding: 12px;">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateEstimate() {
    const roomType = document.getElementById('roomType');
    const checkin = document.getElementById('checkinDate');
    const checkout = document.getElementById('checkoutDate');
    const availableRooms = document.getElementById('availableRooms');
    const estimateDetails = document.getElementById('estimateDetails');
    const estimateAmount = document.getElementById('estimateAmount');
    
    if (roomType.value) {
        const option = roomType.options[roomType.selectedIndex];
        const price = parseInt(option.dataset.price);
        const available = parseInt(option.dataset.available);
        
        availableRooms.value = available + ' phòng';
        availableRooms.style.color = available > 0 ? '#10b981' : '#ef4444';
        
        if (checkin.value && checkout.value) {
            const date1 = new Date(checkin.value);
            const date2 = new Date(checkout.value);
            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                const total = diffDays * price;
                estimateDetails.textContent = `${diffDays} đêm × ${price.toLocaleString()} VNĐ`;
                estimateAmount.textContent = total.toLocaleString() + ' VNĐ';
            }
        }
    }
}

// Validate form
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const roomType = document.getElementById('roomType');
    const option = roomType.options[roomType.selectedIndex];
    const available = parseInt(option.dataset.available);
    
    if (available <= 0) {
        e.preventDefault();
        alert('Loại phòng này đã hết! Vui lòng chọn loại khác.');
        return false;
    }
    
    const checkin = new Date(document.getElementById('checkinDate').value);
    const checkout = new Date(document.getElementById('checkoutDate').value);
    
    if (checkout <= checkin) {
        e.preventDefault();
        alert('Ngày trả phòng phải sau ngày nhận phòng!');
        return false;
    }
});

// Auto-update khi load trang nếu có type được chọn
window.onload = function() {
    updateEstimate();
};
</script>