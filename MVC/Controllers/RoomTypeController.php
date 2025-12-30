<?php
class RoomTypeController extends Controller {

    // 1. Hiển thị danh sách
    public function index() {
        $model = $this->model("RoomTypeModel");
        
        $this->view("Master", [
            "Page" => "RoomTypeManage", // Tên file View ở Bước 2
            "page_tab" => "roomtype",   // Để Menu bên trái sáng lên
            "roomTypes" => $model->getAll() // Lấy dữ liệu
        ]);
    }

    // 2. Xử lý Thêm mới hoặc Cập nhật
    public function saveRoomType() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model("RoomTypeModel");
            
            $id = $_POST['MaLoaiPhong'];
            $ten = $_POST['TenLoaiPhong'];
            $gia = $_POST['GiaPhong'];
            $mota = $_POST['MoTaPhong'];
            $isEdit = $_POST['isEdit']; // 0: Thêm, 1: Sửa

            // Validate cơ bản
            if(empty($id) || empty($ten) || empty($gia)) {
                echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
                return;
            }

            if ($isEdit == 0) {
                // --- CHỨC NĂNG THÊM MỚI ---
                // Kiểm tra trùng mã
                $check = $model->getById($id);
                if ($check) {
                    echo "<script>alert('Mã loại phòng này đã tồn tại!'); window.history.back();</script>";
                } else {
                    $model->insert($id, $ten, $gia, $mota);
                    echo "<script>alert('Thêm mới thành công!'); window.location.href='?controller=RoomTypeController&action=index';</script>";
                }
            } else {
                // --- CHỨC NĂNG CẬP NHẬT ---
                $model->update($id, $ten, $gia, $mota);
                echo "<script>alert('Cập nhật thành công!'); window.location.href='?controller=RoomTypeController&action=index';</script>";
            }
        }
    }

    // 3. Xóa loại phòng
    public function deleteRoomType() {
        if (isset($_GET['id'])) {
            $model = $this->model("RoomTypeModel");
            // Lưu ý: Nếu loại phòng này đang có phòng sử dụng, DB sẽ báo lỗi ràng buộc khóa ngoại
            // Bạn có thể thêm try-catch hoặc kiểm tra trước nếu muốn kỹ hơn
            $model->delete($_GET['id']);
            header("Location: ?controller=RoomTypeController&action=index");
        }
    }

    // 4. Xuất Excel (Dùng mẹo HTML Table - An toàn tuyệt đối)
    public function exportExcel() {
        $model = $this->model("RoomTypeModel");
        $data = $model->getAll();

        $filename = "Danh_Sach_Loai_Phong_" . date('Ymd') . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; // BOM UTF-8

        echo '<table border="1">';
        echo '<tr>
                <th>Mã Loại</th>
                <th>Tên Loại Phòng</th>
                <th>Giá Phòng</th>
                <th>Mô Tả</th>
              </tr>';

        while ($row = mysqli_fetch_array($data)) {
            echo '<tr>';
            echo '<td>' . $row['MaLoaiPhong'] . '</td>';
            echo '<td>' . $row['TenLoaiPhong'] . '</td>';
            echo '<td>' . number_format($row['GiaPhong'], 0, ',', '.') . ' VNĐ</td>';
            echo '<td>' . $row['MoTaPhong'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit();
    }

    // 5. Nhập Excel (Logic chuẩn giống RoomController)
    public function importExcel() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];
            
            // Đường dẫn tuyệt đối đến thư viện
            $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

            if (file_exists($libPath)) {
                require_once $libPath;
            } else {
                die("Không tìm thấy thư viện tại: " . $libPath);
            }

            try {
                // Tự động nhận diện file (.xls hoặc .xlsx)
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("RoomTypeModel");
                $countSuccess = 0;

                for ($row = 1; $row <= $highestRow; $row++) {
                    // Cột A: Mã, B: Tên, C: Giá, D: Mô tả
                    $id   = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                    $ten  = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $gia  = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $mota = $sheet->getCellByColumnAndRow(3, $row)->getValue();

                    if (!empty($id) && !empty($ten)) {
                        // Kiểm tra trùng
                        $check = $model->getById($id);
                        if (!$check) {
                            $model->insert($id, $ten, $gia, $mota);
                            $countSuccess++;
                        }
                    }
                }
                
                echo "<script>
                    alert('Đã nhập thành công $countSuccess loại phòng!'); 
                    window.location.href='?controller=RoomTypeController&action=index';
                </script>";

            } catch (Exception $e) {
                echo "<script>
                    alert('Lỗi đọc file: " . $e->getMessage() . "');
                    window.history.back();
                </script>";
            }
        }
    }
}
?>