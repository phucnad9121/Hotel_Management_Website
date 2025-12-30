<?php
class EmployeeController extends controller {
    
    public function __construct() {
        $this->empModel = $this->model("EmployeeModel");
    }

    public function index() {
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $employees = $this->empModel->getList($keyword);
        $departments = $this->empModel->getDepartments();

        ob_start();
        $this->view("Pages/Employee", [
            "employees" => $employees,
            "departments" => $departments,
            "keyword" => $keyword
        ]);
        $content = ob_get_clean();

        // page_tab = 'employee' để khớp với Master.php
        $this->view("Master", [
            "content" => $content, 
            "page_tab" => "employee" 
        ]);
    }

    // Xử lý lưu (Thêm/Sửa)
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->empModel->save($_POST, $_POST['isEdit']);
            if ($result) {
                echo "<script>alert('Thành công!'); window.location.href='?controller=EmployeeController&action=index';</script>";
            } else {
                echo "<script>alert('Lỗi khi lưu dữ liệu!'); window.history.back();</script>";
            }
        }
    }

    // Xử lý xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if ($this->empModel->delete($id)) {
                header("Location: ?controller=EmployeeController&action=index");
            }
        }
    }

    // Xuất Excel
    public function exportExcel() {
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $employees = $this->empModel->getList($keyword);

    $filename = "Danh_Sach_Nhan_Vien_" . date('Ymd') . ".xls";
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF"; // hỗ trợ tiếng Việt không lỗi font

    echo '<table border="1">';
    echo '<tr style="background-color:#38bdf8; color:#ffffff; font-weight:bold;">
            <th>Mã NV</th>
            <th>Họ & Tên</th>
            <th>Email</th>
            <th>Số Điện Thoại</th>
            <th>CMND/CCCD</th>
            <th>Bộ Phận</th>
            <th>Chức Danh</th>
            <th>Ngày Vào Làm</th>
            <th>Địa Chỉ</th>
          </tr>';

    if (!empty($employees)) {
        foreach ($employees as $row) {
            echo '<tr>';
            echo '<td>'.$row['MaNhanVien'].'</td>';
            echo '<td>'.$row['HoNhanVien'].' '.$row['TenNhanVien'].'</td>';
            echo '<td>'.$row['EmailNhanVien'].'</td>';
            echo '<td>'.$row['SoDienThoaiNV'].'</td>';
            echo '<td>'.$row['CMND_CCCD'].'</td>';
            echo '<td>'.$row['TenBoPhan'].'</td>';
            echo '<td>'.$row['ChucDanhNV'].'</td>';
            echo '<td>'.$row['NgayVaoLam'].'</td>';
            echo '<td>'.$row['DiaChi'].'</td>';
            echo '</tr>';
        }
    }

    echo '</table>';
    exit();
}    
    
    // Import Excel
    public function importExcel() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
        $file = $_FILES['excel_file']['tmp_name'];
        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

        if (file_exists($libPath)) {
            require_once $libPath;
        } else {
            die("Không tìm thấy thư viện tại: " . $libPath);
        }

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $model = $this->model("EmployeeModel");
            $successCount = 0;

            // Bắt đầu từ dòng 2 để bỏ tiêu đề file excel
            for ($row = 2; $row <= $highestRow; $row++) {

                $MaNhanVien   = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                $HoNhanVien   = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $TenNhanVien  = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                $SoDienThoai  = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $Email        = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                $MaBoPhan     = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                $ChucDanh     = $sheet->getCellByColumnAndRow(6, $row)->getValue();
                $CCCD         = $sheet->getCellByColumnAndRow(7, $row)->getValue();
                $NgayVaoLam   = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                $DiaChi       = $sheet->getCellByColumnAndRow(9, $row)->getValue();

                if (!empty($MaNhanVien)) {
                    if (!$model->checkDuplicate($MaNhanVien)) { 

                        $model->insert(
                            $MaNhanVien, $HoNhanVien, $TenNhanVien, 
                            $SoDienThoai, $Email, $MaBoPhan, 
                            $ChucDanh, $CCCD, $NgayVaoLam, $DiaChi
                        );

                        $successCount++;
                    }
                }
            }

            echo "<script>
                alert('Import thành công! Đã thêm $successCount nhân viên.');
                window.location.href='?controller=EmployeeController&action=index';
            </script>";
            
        } catch (Exception $e) {
            die("Lỗi đọc file Excel: " . $e->getMessage());
        }
    }
}

}
