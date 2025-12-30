<?php
class AccountController extends controller {
    public function index() {
        $model = $this->model("AccountModel");
        $accounts = $model->getAllLoginAccounts();

        if (isset($_POST['search'])) {
            $accounts = $model->searchLoginAccounts($_POST['keyword']);
        }

        $employees = $model->getEmployees();

        ob_start();
        $this->view("Pages/Account", [
            "accounts" => $accounts,
            "employees" => $employees
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "account"]);
    }

    public function saveAccount() {
        $model = $this->model("AccountModel");

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = trim($_POST['MaDangNhap'] ?? '');
            $employeeId = trim($_POST['MaNhanVien'] ?? '');
            $username = trim($_POST['TenDangNhap'] ?? '');
            $password = $_POST['MatKhau'] ?? '';
            $isNew = $_POST['NguoiDungMoi'] ?? 'Yes';
            $isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] == "1";

            if ($id === '' || $employeeId === '' || $username === '') {
                echo "<script>alert('Vui long dien day du thong tin!'); window.history.back();</script>";
                return;
            }

            if ($isNew !== 'Yes' && $isNew !== 'No') {
                $isNew = 'Yes';
            }

            if ($isEdit) {
                if ($model->checkDuplicateLoginUsername($username, $id)) {
                    echo "<script>alert('Ten dang nhap da ton tai!'); window.history.back();</script>";
                    return;
                }
                $model->updateLoginAccount($id, $username, $password, $employeeId, $isNew);
            } else {
                if ($password === '') {
                    echo "<script>alert('Vui long nhap mat khau!'); window.history.back();</script>";
                    return;
                }
                if ($model->checkDuplicateLoginId($id)) {
                    echo "<script>alert('Ma dang nhap nay da ton tai!'); window.history.back();</script>";
                    return;
                }
                if ($model->checkDuplicateLoginUsername($username)) {
                    echo "<script>alert('Ten dang nhap da ton tai!'); window.history.back();</script>";
                    return;
                }
                $model->insertLoginAccount($id, $username, $password, $employeeId, $isNew);
            }

            header("Location: ?controller=AccountController&action=index");
            exit();
        }
    }

    public function deleteAccount() {
        if (isset($_GET['id'])) {
            $model = $this->model("AccountModel");
            $model->deleteLoginAccount($_GET['id']);
            header("Location: ?controller=AccountController&action=index");
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

                $model = $this->model("AccountModel");
                $successCount = 0;

                // Cot: A=MaDangNhap, B=TenDangNhap, C=MatKhau, D=MaNhanVien, E=NguoiDungMoi
                for ($row = 2; $row <= $highestRow; $row++) {
                    $id = trim((string)$sheet->getCellByColumnAndRow(0, $row)->getValue());
                    $username = trim((string)$sheet->getCellByColumnAndRow(1, $row)->getValue());
                    $password = (string)$sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $employeeId = trim((string)$sheet->getCellByColumnAndRow(3, $row)->getValue());
                    $isNew = trim((string)$sheet->getCellByColumnAndRow(4, $row)->getValue());

                    if ($isNew !== 'Yes' && $isNew !== 'No') {
                        $isNew = 'Yes';
                    }

                    if ($id === '' || $username === '' || $password === '' || $employeeId === '') {
                        continue;
                    }

                    if ($model->checkDuplicateLoginId($id) || $model->checkDuplicateLoginUsername($username)) {
                        continue;
                    }

                    $model->insertLoginAccount($id, $username, $password, $employeeId, $isNew);
                    $successCount++;
                }

                echo "<script>
                    alert('Thanh cong! Da them $successCount tai khoan moi.');
                    window.location.href='?controller=AccountController&action=index';
                </script>";
            } catch (Exception $e) {
                die("Loi doc file Excel: " . $e->getMessage());
            }
        }
    }

    public function exportExcel() {
        $model = $this->model("AccountModel");
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $accounts = !empty($keyword) ? $model->searchLoginAccounts($keyword) : $model->getAllLoginAccounts();

        $filename = "Danh_Sach_Tai_Khoan_" . date('Ymd') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";

        echo '<table border="1">';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th>MaDangNhap</th>
                <th>TenDangNhap</th>
                <th>MaNhanVien</th>
                <th>NhanVien</th>
                <th>NguoiDungMoi</th>
              </tr>';

        if (!empty($accounts)) {
            foreach ($accounts as $row) {
                echo '<tr>';
                echo '<td>' . $row['MaDangNhap'] . '</td>';
                echo '<td>' . $row['TenDangNhap'] . '</td>';
                echo '<td>' . $row['MaNhanVien'] . '</td>';
                echo '<td>' . $row['NhanVien'] . '</td>';
                echo '<td>' . $row['NguoiDungMoi'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">Khong co du lieu tim thay</td></tr>';
        }

        echo '</table>';
        exit();
    }
}

