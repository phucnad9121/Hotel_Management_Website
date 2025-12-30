<?php
class RoomController extends Controller {

    public function index() {
        // 1. Load Models
        $roomModel = $this->model("RoomModel");
        $roomTypeModel = $this->model("RoomTypeModel"); // Cần file này để lấy Dropdown

        // 2. Xử lý tìm kiếm
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

        // 3. Gửi dữ liệu sang View
        $this->view("Master", [
            "Page" => "RoomManage", // Tên file View ở Bước 3
            "rooms" => $roomModel->getAll($keyword),
            "roomTypes" => $roomTypeModel->getAll(), // Dữ liệu cho Combobox
            "page_tab" => "room"
            
        ]);
    }

    // Xử lý Thêm hoặc Sửa
    public function saveRoom() {
        if(isset($_POST['MaPhong'])) {
            $model = $this->model("RoomModel");
            
            $id = $_POST['MaPhong'];
            $soPhong = $_POST['SoPhong'];
            $maLoai = $_POST['MaLoaiPhong'];
            $khaDung = $_POST['KhaDung'];
            $isEdit = $_POST['isEdit']; // 0 là Thêm, 1 là Sửa

            if($isEdit == 0) {
                // Check trùng mã trước khi thêm
                if(mysqli_num_rows($model->checkExists($id)) > 0){
                    echo "<script>alert('Mã phòng này đã tồn tại!'); window.history.back();</script>";
                } else {
                    $model->insert($id, $soPhong, $maLoai, $khaDung);
                }
            } else {
                $model->updateRoomInfo($id, $soPhong, $maLoai, $khaDung);
            }
            
            // Quay về trang chủ
            header("Location: ?controller=RoomController&action=index");
        }
    }

    public function deleteRoom() {
        if(isset($_GET['id'])) {
            $model = $this->model("RoomModel");
            $model->delete($_GET['id']);
            header("Location: ?controller=RoomController&action=index");
        }
    }

    // --- PHẦN EXCEL (Copy logic từ DepartmentController) ---
    
    public function exportExcel() {
        // 1. Gọi Model lấy dữ liệu
        $roomModel = $this->model("RoomModel");
        
        // Kiểm tra xem có từ khóa tìm kiếm không (Logic giống Department)
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        
        if (!empty($keyword)) {
            $rooms = $roomModel->getAll($keyword);
        } else {
            $rooms = $roomModel->getAll();
        }

        // 2. Đặt tên file
        $filename = "Danh_Sach_Phong_" . date('Ymd') . ".xls";

        // 3. Cấu hình Header để đánh lừa trình duyệt đây là Excel
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; // Quan trọng: Mã BOM để hiển thị tiếng Việt không bị lỗi font

        // 4. Xuất bảng dữ liệu dưới dạng HTML Table
        echo '<table border="1">';
        // Dòng tiêu đề
        echo '<tr>
                <th>Mã Phòng</th>
                <th>Số Phòng</th>
                <th>Loại Phòng</th>
                <th>Trạng Thái</th>
              </tr>';

        // Dòng dữ liệu
        if ($rooms) {
            while ($row = mysqli_fetch_array($rooms)) {
                // Xử lý hiển thị trạng thái cho đẹp
                $statusText = ($row['KhaDung'] == 'Yes') ? 'Sẵn sàng' : 'Bảo trì';
                
                echo '<tr>';
                echo '<td>' . $row['MaPhong'] . '</td>';
                echo '<td>' . $row['SoPhong'] . '</td>';
                echo '<td>' . $row['TenLoaiPhong'] . '</td>'; // Cột này lấy từ JOIN bảng
                echo '<td>' . $statusText . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">Không có dữ liệu phòng nào.</td></tr>';
        }
        echo '</table>';
        exit();
    }

    public function importExcel() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            // 1. Sử dụng PATH tuyệt đối giống hệt bên Department
            $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

            if (file_exists($libPath)) {
                require_once $libPath;
            } else {
                die("Không tìm thấy thư viện tại: " . $libPath);
            }

            try {
                // 2. Dùng hàm load tự động nhận diện file (giống bên Department)
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("RoomModel");
                $successCount = 0;

                // 3. Chạy vòng lặp từ dòng 2 (bỏ qua tiêu đề)
                for ($row = 1; $row <= $highestRow; $row++) {
                    // Đọc dữ liệu từng cột theo cấu trúc file Phòng
                    $id      = $sheet->getCellByColumnAndRow(0, $row)->getValue(); // Cột A: Mã Phòng
                    $soPhong = $sheet->getCellByColumnAndRow(1, $row)->getValue(); // Cột B: Số Phòng
                    $maLoai  = $sheet->getCellByColumnAndRow(2, $row)->getValue(); // Cột C: Mã Loại
                    $khaDung = $sheet->getCellByColumnAndRow(3, $row)->getValue(); // Cột D: Trạng thái

                    if (!empty($id)) {
                        // Kiểm tra trùng mã (Sử dụng hàm checkExists của RoomModel)
                        // Lưu ý: checkExists trả về object, cần dùng mysqli_num_rows để đếm
                        $check = $model->checkExists($id);
                        if (mysqli_num_rows($check) == 0) {
                            
                            // Xử lý dữ liệu mặc định nếu trống
                            if(empty($khaDung)) $khaDung = 'Yes';
                            
                            // Thêm mới
                            $model->insert($id, $soPhong, $maLoai, $khaDung);
                            $successCount++;
                        }
                    }
                }
                
                echo "<script>
                    alert('Thành công! Đã thêm $successCount phòng mới.');
                    window.location.href='?controller=RoomController&action=index';
                </script>";

            } catch (Exception $e) {
                die("Lỗi đọc file Excel: " . $e->getMessage());
            }
        }
    }
}
?>