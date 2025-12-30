<?php
class ServiceController extends controller {
    
    // ==================== CÁC HÀM CŨ GIỮ NGUYÊN ====================

    // Hiển thị danh sách dịch vụ (Quản trị viên)
    public function index() {
        $model = $this->model("ServiceModel");
        
        // Xử lý tìm kiếm
        if (isset($_POST['search'])) {
            $services = $model->search($_POST['keyword']);
        } else {
            $services = $model->getAll();
        }
        
        ob_start();
        $this->view("Pages/ServiceManage", ["services" => $services]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "service"]);
    }
    
    // Xử lý thêm/sửa dịch vụ
    public function saveService() {
        $model = $this->model("ServiceModel");
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] == "1";
            $id = strtoupper(trim($_POST['MaDichVu'])); // CHỮ HOA, BỎ KHOẢNG TRẮNG
            $name = trim($_POST['TenDichVu']);
            $desc = trim($_POST['MoTaDichVu']);
            $price = floatval($_POST['ChiPhiDichVu']);
            
            // Validate mã dịch vụ
            if (empty($id)) {
                echo "<script>alert('Vui lòng nhập mã dịch vụ!'); window.history.back();</script>";
                return;
            }
            
            if (strlen($id) < 2 || strlen($id) > 50) {
                echo "<script>alert('Mã dịch vụ phải từ 2-50 ký tự!'); window.history.back();</script>";
                return;
            }
            
            // Validate các trường khác
            if (empty($name) || $price <= 0) {
                echo "<script>alert('Vui lòng điền đầy đủ thông tin hợp lệ!'); window.history.back();</script>";
                return;
            }
            
            if ($isEdit) {
                // KHI SỬA: Kiểm tra trùng tên (trừ chính nó)
                if ($model->checkDuplicate($name, $id)) {
                    echo "<script>alert('Tên dịch vụ đã tồn tại!'); window.history.back();</script>";
                    return;
                }
                
                if ($model->update($id, $name, $desc, $price)) {
                    echo "<script>alert('Cập nhật dịch vụ thành công!'); window.location.href='?controller=ServiceController&action=index';</script>";
                } else {
                    echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
                }
            } else {
                // KHI THÊM: Kiểm tra trùng MÃ
                if ($model->checkIdExists($id)) {
                    echo "<script>alert('Mã dịch vụ \"$id\" đã tồn tại! Vui lòng chọn mã khác.'); window.history.back();</script>";
                    return;
                }
                
                // Kiểm tra trùng tên
                if ($model->checkDuplicate($name, null)) {
                    echo "<script>alert('Tên dịch vụ đã tồn tại!'); window.history.back();</script>";
                    return;
                }
                
                if ($model->insertWithId($id, $name, $desc, $price)) {
                    echo "<script>alert('Thêm dịch vụ thành công!'); window.location.href='?controller=ServiceController&action=index';</script>";
                } else {
                    echo "<script>alert('Thêm dịch vụ thất bại! Vui lòng kiểm tra lại.'); window.history.back();</script>";
                }
            }
        }
    }
    
    // Xóa dịch vụ
    public function deleteService() {
        // Kiểm tra ID có tồn tại
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo "<script>
                alert('Lỗi: Không tìm thấy mã dịch vụ!');
                window.location.href='?controller=ServiceController&action=index';
            </script>";
            return;
        }
        
        // LẤY ID DẠNG STRING, KHÔNG VALIDATE INT
        $id = trim($_GET['id']);
        
        // Kiểm tra độ dài hợp lệ
        if (strlen($id) < 2 || strlen($id) > 50) {
            echo "<script>
                alert('Mã dịch vụ không hợp lệ! (2-50 ký tự)');
                window.location.href='?controller=ServiceController&action=index';
            </script>";
            return;
        }
        
        $model = $this->model("ServiceModel");
        
        // Kiểm tra dịch vụ có tồn tại không
        $service = $model->getById($id);
        if (!$service) {
            echo "<script>
                alert('Không tìm thấy dịch vụ này!');
                window.location.href='?controller=ServiceController&action=index';
            </script>";
            return;
        }
        
        // Thử xóa
        if ($model->delete($id)) {
            echo "<script>
                alert('Xóa dịch vụ thành công!');
                window.location.href='?controller=ServiceController&action=index';
            </script>";
        } else {
            echo "<script>
                alert('Xóa thất bại! Dịch vụ đang được sử dụng trong các booking.');
                window.location.href='?controller=ServiceController&action=index';
            </script>";
        }
    }
    
    // API lấy danh sách dịch vụ (cho khách hàng chọn)
    public function getAvailableServices() {
        // [FIX] Xóa buffer để tránh lỗi JSON
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        $model = $this->model("ServiceModel");
        $services = $model->getAll();
        echo json_encode($services);
        exit();
    }

    // Thêm dịch vụ cho booking (từ giao diện khách)
    public function addToBooking() {
        return $this->addServiceUsed();
    }
    
    // ==================== CÁC HÀM ĐÃ SỬA LỖI JSON ====================

    // Thêm dịch vụ cho booking (từ giao diện khách/admin)
    public function addServiceUsed() {
        // [FIX] Xóa sạch bộ đệm đầu ra để đảm bảo không có ký tự lạ làm hỏng JSON
        if (ob_get_length()) ob_clean();
        
        // [FIX] Chỉ start session nếu chưa có, tránh lỗi "Session already started"
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        header('Content-Type: application/json'); 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate dữ liệu đầu vào
            if (!isset($_POST['ma_dat_phong']) || !isset($_POST['ma_dich_vu'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
                exit();
            }
            
            $model = $this->model("ServiceModel");
            $maDatPhong = intval($_POST['ma_dat_phong']);
            $maDichVu = ($_POST['ma_dich_vu']);
            
            // [FIX] Sử dụng hàm trong Model thay vì hàm private gây lỗi kết nối
            if ($model->checkServiceUsedExists($maDatPhong, $maDichVu)) {
                echo json_encode(['success' => false, 'message' => 'Dịch vụ đã được đặt trước đó']);
                exit();
            }
            
            if ($model->addServiceUsed($maDatPhong, $maDichVu)) {
                echo json_encode(['success' => true, 'message' => 'Đã thêm dịch vụ']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Thêm thất bại']);
            }
        }
        exit();
    }
    
    // Xóa dịch vụ khỏi booking
    public function removeFromBooking() {
        // [FIX] Xóa sạch bộ đệm đầu ra
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        
        // Hỗ trợ cả POST và GET cho linh hoạt
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID dịch vụ']); 
            exit();
        }
        
        $model = $this->model("ServiceModel");
        
        // Gọi đúng hàm removeServiceUsed trong Model
        if ($model->removeServiceUsed($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi Database không thể xóa']);
        }
        exit();
    }
}
?>