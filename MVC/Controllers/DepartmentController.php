<?php
class DepartmentController extends controller {
    // Hiển thị danh sách bộ phận
    public function index() {
        $model = $this->model("DepartmentModel");
        $departments = $model->getAll();

        if (isset($_POST['search'])) {
            $departments = $model->search($_POST['keyword']);
        }

        ob_start();
        $this->view("Pages/Department", ["departments" => $departments]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "department"]);
    }

    // Action xử lý Thêm/Sửa
    public function saveDepartment() {
        $model = $this->model("DepartmentModel");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['MaBoPhan'];
            $name = $_POST['TenBoPhan'];
            $desc = $_POST['MoTaBoPhan'];
            $salary = $_POST['LuongKhoiDiem'];
            $title = $_POST['ChucDanh'];
            $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] == "1";

            if (empty($id) || empty($name) || empty($salary)) {
                echo "<script>alert('Vui lòng điền đủ thông tin!'); window.history.back();</script>";
                return;
            }

            if ($isEdit) {
                $model->update($id, $name, $desc, $salary, $title);
            } else {
                if ($model->checkDuplicate($id)) {
                    echo "<script>alert('Mã này đã tồn tại!'); window.history.back();</script>";
                    return;
                }
                $model->insert($id, $name, $desc, $salary, $title);
            }
            
            header("Location: ?controller=DepartmentController&action=index");
            exit(); // Thêm exit để dừng thực thi
        }
    } // Đóng hàm saveDepartment

    public function deleteDepartment() {
        if (isset($_GET['id'])) {
            $model = $this->model("DepartmentModel");
            $model->delete($_GET['id']);
            
            header("Location: ?controller=DepartmentController&action=index");
            exit();
        }
    }

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
                $model = $this->model("DepartmentModel");
                $successCount = 0;

                for ($row = 2; $row <= $highestRow; $row++) {
                    $id     = $sheet->getCellByColumnAndRow(0, $row)->getValue();
                    $name   = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $desc   = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $salary = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                    $title  = $sheet->getCellByColumnAndRow(4, $row)->getValue();

                    if (!empty($id)) {
                        if (!$model->checkDuplicate($id)) {
                            $model->insert($id, $name, $desc, (int)$salary, $title);
                            $successCount++;
                        }
                    }
                }
                
                echo "<script>
                    alert('Thành công! Đã thêm $successCount bộ phận mới.');
                    window.location.href='?controller=DepartmentController&action=index';
                </script>";
            } catch (Exception $e) {
                die("Lỗi đọc file Excel: " . $e->getMessage());
            }
        }
    }

    public function exportExcel() {
        $model = $this->model("DepartmentModel");
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $departments = !empty($keyword) ? $model->search($keyword) : $model->getAll();

        $filename = "Danh_Sach_Bo_Phan_" . date('Ymd') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; 

        echo '<table border="1">';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th>Mã Bộ Phận</th><th>Tên Bộ Phận</th><th>Mô Tả</th><th>Lương Khởi Điểm</th><th>Chức Danh</th>
              </tr>';

        if (!empty($departments)) {
            foreach ($departments as $row) {
                echo '<tr>';
                echo '<td>' . $row['MaBoPhan'] . '</td>';
                echo '<td>' . $row['TenBoPhan'] . '</td>';
                echo '<td>' . $row['MoTaBoPhan'] . '</td>';
                echo '<td>' . number_format($row['LuongKhoiDiem'], 0, ',', '.') . '</td>';
                echo '<td>' . $row['ChucDanh'] . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        exit();
    }
} // Kết thúc class DepartmentController ở ĐÂY