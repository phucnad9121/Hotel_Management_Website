<?php
class DiscountController extends controller {
    public function index() {
        $discountModel = $this->model("DiscountModel");
        $employeeModel = $this->model("EmployeeModel");

        $discounts = $discountModel->getAll();
        if (isset($_POST['search'])) {
            $discounts = $discountModel->search($_POST['keyword']);
        }

        $employees = $employeeModel->getList();

        ob_start();
        $this->view("Pages/Discount", [
            "discounts" => $discounts,
            "employees" => $employees
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "discount"]);
    }

    public function saveDiscount() {
        $model = $this->model("DiscountModel");

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = trim($_POST['MaGiamGia'] ?? '');
            $name = trim($_POST['TenGiamGia'] ?? '');
            $desc = $_POST['MoTaGiamGia'] ?? '';
            $rate = $_POST['TyLeGiamGia'] ?? '';
            $employeeId = trim($_POST['MaNhanVien'] ?? '');
            $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] == "1";

            if ($id === '' || $name === '' || $rate === '' || $employeeId === '') {
                echo "<script>alert('Vui long dien day du thong tin!'); window.history.back();</script>";
                return;
            }

            if (!is_numeric($rate) || (int)$rate < 0 || (int)$rate > 100) {
                echo "<script>alert('Ty le giam phai la so tu 0 den 100!'); window.history.back();</script>";
                return;
            }
            $rate = (int)$rate;

            if ($isEdit) {
                $model->update($id, $name, $desc, $rate, $employeeId);
            } else {
                if ($model->checkDuplicate($id)) {
                    echo "<script>alert('Ma giam gia nay da ton tai!'); window.history.back();</script>";
                    return;
                }
                $model->insert($id, $name, $desc, $rate, $employeeId);
            }

            header("Location: ?controller=DiscountController&action=index");
            exit();
        }
    }

    public function deleteDiscount() {
        if (isset($_GET['id'])) {
            $model = $this->model("DiscountModel");
            $model->delete($_GET['id']);
            header("Location: ?controller=DiscountController&action=index");
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
                die("Khong tim thay thu vien tai: " . $libPath);
            }

            try {
                $objPHPExcel = PHPExcel_IOFactory::load($file);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                $model = $this->model("DiscountModel");
                $successCount = 0;

                // Cot: A=MaGiamGia, B=TenGiamGia, C=MoTaGiamGia, D=TyLeGiamGia, E=MaNhanVien
                for ($row = 2; $row <= $highestRow; $row++) {
                    $id = trim((string)$sheet->getCellByColumnAndRow(0, $row)->getValue());
                    $name = (string)$sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $desc = (string)$sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $rate = (int)$sheet->getCellByColumnAndRow(3, $row)->getValue();
                    $employeeId = trim((string)$sheet->getCellByColumnAndRow(4, $row)->getValue());

                    if ($id === '' || $name === '' || $employeeId === '') {
                        continue;
                    }

                    if ($rate < 0 || $rate > 100) {
                        continue;
                    }

                    if (!$model->checkDuplicate($id)) {
                        $model->insert($id, $name, $desc, $rate, $employeeId);
                        $successCount++;
                    }
                }

                echo "<script>
                    alert('Thanh cong! Da them $successCount giam gia moi.');
                    window.location.href='?controller=DiscountController&action=index';
                </script>";
            } catch (Exception $e) {
                die("Loi doc file Excel: " . $e->getMessage());
            }
        }
    }

    public function exportExcel() {
        $model = $this->model("DiscountModel");

        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $discounts = !empty($keyword) ? $model->search($keyword) : $model->getAll();

        $filename = "Danh_Sach_Giam_Gia_" . date('Ymd') . ".xls";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";

        echo '<table border="1">';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th>Ma</th>
                <th>Ten giam gia</th>
                <th>Mo ta</th>
                <th>Ty le (%)</th>
                <th>Ma nhan vien</th>
                <th>Nguoi tao</th>
              </tr>';

        if (!empty($discounts)) {
            foreach ($discounts as $row) {
                echo '<tr>';
                echo '<td>' . $row['MaGiamGia'] . '</td>';
                echo '<td>' . $row['TenGiamGia'] . '</td>';
                echo '<td>' . $row['MoTaGiamGia'] . '</td>';
                echo '<td>' . (int)$row['TyLeGiamGia'] . '</td>';
                echo '<td>' . $row['MaNhanVien'] . '</td>';
                echo '<td>' . $row['NguoiTao'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Khong co du lieu tim thay</td></tr>';
        }

        echo '</table>';
        exit();
    }
}
