<style>
.service-wrapper {
    min-height: 100vh;
    background: var(--bg-dark);
    padding: 40px 20px;
}
.service-container {
    max-width: 1200px;
    margin: 0 auto;
}
.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.service-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 20px;
    border: 2px solid transparent;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
}
.service-card:hover {
    border-color: var(--ocean-blue);
    transform: translateY(-3px);
}
.service-card.selected {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}
.service-icon {
    font-size: 2.5rem;
    color: var(--ocean-blue);
    margin-bottom: 10px;
}
.service-name {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--text-white);
    margin-bottom: 8px;
}
.service-desc {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 12px;
    min-height: 40px;
}
.service-price {
    font-size: 1.3rem;
    color: #10b981;
    font-weight: bold;
}
.selected-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #10b981;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}
.summary-box {
    background: linear-gradient(135deg, var(--ocean-blue), #0369a1);
    color: white;
    padding: 25px;
    border-radius: 12px;
    position: sticky;
    top: 20px;
}
.summary-total {
    font-size: 2rem;
    font-weight: bold;
    text-align: center;
    margin: 15px 0;
}
.selected-list {
    background: rgba(255,255,255,0.1);
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    max-height: 300px;
    overflow-y: auto;
}
.selected-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.search-box {
    background: var(--card-bg);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}
</style>

<div class="service-wrapper">
    <div class="service-container">
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: var(--text-white); font-size: 2rem;">
                <i class="fas fa-concierge-bell"></i> ĐẶT DỊCH VỤ
            </h1>
            <p style="color: var(--text-muted);">
                Booking #<?= $data['booking']['MaDatPhong'] ?> - 
                <?= $data['booking']['HoKhachHang'] ?> <?= $data['booking']['TenKhachHang'] ?>
            </p>
        </div>

        <!-- Tìm kiếm -->
        <div class="search-box">
            <input type="text" id="searchService" 
                   placeholder="🔍 Tìm kiếm dịch vụ..." 
                   class="form-control-booking"
                   onkeyup="filterServices()"
                   style="margin: 0;">
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Danh sách dịch vụ -->
            <div>
                <h3 style="color: var(--ocean-blue); margin-bottom: 20px;">
                    <i class="fas fa-list"></i> Chọn dịch vụ
                </h3>
                <div class="service-grid" id="serviceGrid">
                    <?php foreach($data['services'] as $service): ?>
                    <div class="service-card" 
                         data-id="<?= $service['MaDichVu'] ?>"
                         data-name="<?= htmlspecialchars($service['TenDichVu']) ?>"
                         data-price="<?= $service['ChiPhiDichVu'] ?>"
                         onclick="toggleService(this)">
                        <div class="service-name"><?= $service['TenDichVu'] ?></div>
                        <div class="service-desc"><?= $service['MoTaDichVu'] ?></div>
                        <div class="service-price"><?= number_format($service['ChiPhiDichVu']) ?> đ</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tổng kết -->
            <div>
                <div class="summary-box">
                    <h3 style="margin: 0 0 15px 0; text-align: center;">
                        <i class="fas fa-receipt"></i> Tổng Kết
                    </h3>
                    
                    <div class="selected-list" id="selectedList">
                        <p style="text-align: center; opacity: 0.7;">Chưa chọn dịch vụ nào</p>
                    </div>
                    
                    <div style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span>Số lượng:</span>
                            <strong id="serviceCount">0</strong>
                        </div>
                        <div class="summary-total" id="totalAmount">0 đ</div>
                    </div>
                    
                    <button class="btn-submit" style="width: 100%; margin-top: 15px;" onclick="confirmOrder()">
                        <i class="fas fa-check-circle"></i> Xác nhận đặt dịch vụ
                    </button>
                    
                    <a href="?controller=GuestController&action=home" 
                       class="btn-back" 
                       style="width: 100%; text-align: center; display: block; margin-top: 10px;">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const selectedServices = new Map();
const bookingId = <?= json_encode($data['booking']['MaDatPhong'] ?? '') ?>;

function toggleService(card) {
    const id = card.dataset.id;
    const name = card.dataset.name;
    const price = parseInt(card.dataset.price);
    
    if (selectedServices.has(id)) {
        selectedServices.delete(id);
        card.classList.remove('selected');
        const badge = card.querySelector('.selected-badge');
        if (badge) badge.remove();
    } else {
        selectedServices.set(id, { name, price });
        card.classList.add('selected');
        const badge = document.createElement('div');
        badge.className = 'selected-badge';
        badge.innerHTML = '<i class="fas fa-check"></i> Đã chọn';
        card.appendChild(badge);
    }
    
    updateSummary();
}

function updateSummary() {
    const listDiv = document.getElementById('selectedList');
    const countSpan = document.getElementById('serviceCount');
    const totalDiv = document.getElementById('totalAmount');
    
    if (selectedServices.size === 0) {
        listDiv.innerHTML = '<p style="text-align: center; opacity: 0.7;">Chưa chọn dịch vụ nào</p>';
        countSpan.textContent = '0';
        totalDiv.textContent = '0 đ';
        return;
    }
    
    let html = '';
    let total = 0;
    
    selectedServices.forEach((service, id) => {
        total += service.price;
        html += `
            <div class="selected-item">
                <span>${service.name}</span>
                <span style="font-weight: bold;">${service.price.toLocaleString()} đ</span>
            </div>
        `;
    });
    
    listDiv.innerHTML = html;
    countSpan.textContent = selectedServices.size;
    totalDiv.textContent = total.toLocaleString() + ' đ';
}

function filterServices() {
    const keyword = document.getElementById('searchService').value.toLowerCase();
    const cards = document.querySelectorAll('.service-card');
    
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        if (name.includes(keyword)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

async function confirmOrder() {
    if (selectedServices.size === 0) {
        alert('Vui lòng chọn ít nhất một dịch vụ!');
        return;
    }
    
    if (!confirm(`Xác nhận đặt ${selectedServices.size} dịch vụ?`)) {
        return;
    }
    
    let successCount = 0;
    
    for (const [id, service] of selectedServices) {
        try {
            const formData = new FormData();
            formData.append('ma_dat_phong', bookingId);
            formData.append('ma_dich_vu', id);
            
            const response = await fetch('?controller=ServiceController&action=addToBooking', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) successCount++;
        } catch (error) {
            console.error('Lỗi:', error);
        }
    }
    
    if (successCount > 0) {
        alert(`Đã đặt thành công ${successCount} dịch vụ!`);
        window.location.href = '?controller=GuestController&action=myBookings';
    } else {
        alert('Đặt dịch vụ thất bại!');
    }
}
</script>
