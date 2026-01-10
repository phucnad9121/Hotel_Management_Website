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

            <div class="estimate-box">
                <div style="font-size: 1.2rem; margin-bottom: 10px;">
                    <i class="fas fa-calculator"></i> Chi phí dự kiến
                </div>
                
                <div id="estimateDetails" style="margin-bottom: 5px; opacity: 0.9;">
                    Chọn ngày để xem ước tính
                </div>
                
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed rgba(255,255,255,0.3);">
                    <span>Tổng giá trị đơn:</span>
                    <span id="totalAmount" style="font-weight: bold;">0 VNĐ</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 10px 0; color: #ffd700; font-size: 1.2rem; font-weight: bold;">
                    <span><i class="fas fa-money-bill-wave"></i> Cần cọc trước (50%):</span>
                    <span id="depositAmount">0 VNĐ</span>
                </div>

                <div style="display: flex; justify-content: space-between; padding: 5px 0; font-size: 0.9rem; font-style: italic;">
                    <span>Thanh toán tại quầy sau:</span>
                    <span id="remainAmount">0 VNĐ</span>
                </div>

                <div id="qrBox" style="display: none; text-align: center; margin-top: 20px; background: white; padding: 15px; border-radius: 10px; color: #333;">
                    <h4 style="color: #333; margin-bottom: 10px; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                        <i class="fas fa-qrcode"></i> QUÉT MÃ ĐỂ ĐẶT CỌC
                    </h4>
                    
                    <img id="qrImage" src="" alt="QR Code" style="max-width: 250px; border: 1px solid #ddd; border-radius: 5px;">
                    
                    <div style="color: #333; margin-top: 10px; font-weight: bold;">
                        Nội dung CK: <span id="qrContent" style="color: #e11d48;">DAT COC</span>
                    </div>
                    <div style="color: #64748b; font-size: 0.85rem; margin-top: 5px; font-style: italic;">
                        * Vui lòng thanh toán trước khi gửi yêu cầu
                    </div>
                </div>
                </div>

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
    // --- CẤU HÌNH TÀI KHOẢN NGÂN HÀNG (SỬA Ở ĐÂY) ---
    const MY_BANK = 'MB';           // Mã ngân hàng (MB, VCB, ACB, BIDV, TECHCOMBANK...)
    const MY_ACCOUNT = '0363294932'; // Số tài khoản của bạn
    const MY_NAME = 'NGUYEN VAN A'; // Tên chủ tài khoản
    // ------------------------------------------------

    const roomType = document.getElementById('roomType');
    const checkin = document.getElementById('checkinDate');
    const checkout = document.getElementById('checkoutDate');
    const availableRooms = document.getElementById('availableRooms');
    const estimateDetails = document.getElementById('estimateDetails');
    
    // Các biến hiển thị tiền
    const totalEl = document.getElementById('totalAmount');
    const depositEl = document.getElementById('depositAmount');
    const remainEl = document.getElementById('remainAmount');

    // Các biến QR (Mới thêm)
    const qrBox = document.getElementById('qrBox');
    const qrImage = document.getElementById('qrImage');
    const qrContent = document.getElementById('qrContent');
    
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
                const deposit = Math.round(total * 0.5); // 50% tiền cọc (làm tròn số)
                const remain = total - deposit; // Số tiền còn lại

                estimateDetails.textContent = `${diffDays} đêm × ${price.toLocaleString()} VNĐ`;
                
                // Cập nhật giao diện tiền
                totalEl.textContent = total.toLocaleString() + ' VNĐ';
                depositEl.textContent = deposit.toLocaleString() + ' VNĐ';
                remainEl.textContent = remain.toLocaleString() + ' VNĐ';

                // --- LOGIC TẠO MÃ QR (MỚI THÊM) ---
                // Lấy tên khách từ PHP để làm nội dung chuyển khoản
                const guestName = "<?= $data['guest']['TenKhachHang'] ?>";
                
                // Viết hoa và bỏ dấu tiếng Việt để nội dung CK sạch đẹp
                const cleanName = guestName.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().replace(/[^A-Z0-9 ]/g, '');
                const contentCK = `COC ${cleanName}`;

                // Tạo link VietQR API
                // Link format: https://img.vietqr.io/image/<BANK>-<ACC>-compact.png?amount=<AMOUNT>&addInfo=<INFO>&accountName=<NAME>
                const qrUrl = `https://img.vietqr.io/image/${MY_BANK}-${MY_ACCOUNT}-compact.png?amount=${deposit}&addInfo=${encodeURIComponent(contentCK)}&accountName=${encodeURIComponent(MY_NAME)}`;

                qrImage.src = qrUrl;
                qrContent.textContent = contentCK;
                qrBox.style.display = 'block';
                // ----------------------------------

            } else {
                qrBox.style.display = 'none'; // Ẩn QR nếu ngày không hợp lệ
            }
        } else {
            qrBox.style.display = 'none'; // Ẩn QR nếu chưa chọn ngày
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