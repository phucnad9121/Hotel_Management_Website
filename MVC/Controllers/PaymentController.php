<?php
class PaymentController extends controller {
    public function index() {
        $model = $this->model("PaymentModel");

        $keyword = "";
        if (isset($_POST['search'])) {
            $keyword = $_POST['keyword'] ?? "";
        }

        $bookings = $model->getBookings($keyword);
        $discounts = $model->getDiscounts();

        ob_start();
        $this->view("Pages/Payment", [
            "bookings" => $bookings,
            "discounts" => $discounts,
            "keyword" => $keyword
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "payment"]);
    }

    public function getServices() {
        $bookingId = $_GET['id'] ?? '';
        $model = $this->model("PaymentModel");

        $services = [];
        if ($bookingId !== '') {
            $services = $model->getServicesByBooking($bookingId);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($services);
        exit();
    }

    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?controller=PaymentController&action=index");
            exit();
        }

        $model = $this->model("PaymentModel");

        $bookingId = trim($_POST['MaDatPhong'] ?? '');
        $discountId = trim($_POST['MaGiamGia'] ?? 'none');
        $method = trim($_POST['PhuongThuc'] ?? 'Cash');

        if ($bookingId === '') {
            echo "<script>alert('Vui lòng chọn booking để thanh toán!'); window.history.back();</script>";
            return;
        }

        $booking = $model->getBookingById($bookingId);
        if (empty($booking)) {
            echo "<script>alert('Không tìm thấy booking!'); window.history.back();</script>";
            return;
        }

        if ($model->paymentExists($bookingId)) {
            echo "<script>alert('Booking này đã được thanh toán!'); window.location.href='?controller=PaymentController&action=index';</script>";
            return;
        }

        if (isset($booking['TrangThai']) && $booking['TrangThai'] === 'Cancelled') {
            echo "<script>alert('Booking đã bị hủy, không thể thanh toán!'); window.history.back();</script>";
            return;
        }

        $roomCost = (int)($booking['SoTienDatPhong'] ?? 0);
        $serviceCost = (int)$model->getServiceTotalByBooking($bookingId);

        $rate = 0;
        if ($discountId !== '' && $discountId !== 'none' && $discountId !== '0') {
            $discount = $model->getDiscountById($discountId);
            if (!empty($discount)) {
                $rate = (int)($discount['TyLeGiamGia'] ?? 0);
            } else {
                $discountId = 'none';
            }
        } else {
            $discountId = 'none';
        }

        if ($rate < 0) $rate = 0;
        if ($rate > 100) $rate = 100;

        $discountAmount = (int)floor(($roomCost + $serviceCost) * $rate / 100);
        $totalCost = $roomCost + $serviceCost - $discountAmount;
        if ($totalCost < 0) $totalCost = 0;

        $ok = $model->createPayment($bookingId, $roomCost, $serviceCost, $totalCost, $method);
        if (!$ok) {
            echo "<script>alert('Lỗi khi tạo thanh toán!'); window.history.back();</script>";
            return;
        }

        $model->updateBookingDiscount($bookingId, $discountId);
        $model->updateBookingStatus($bookingId, 'Checkout');
        $model->releaseRooms($bookingId);
        $model->updateGuestStatusNotReserved($bookingId);

        echo "<script>alert('Thanh toán thành công!'); window.location.href='?controller=PaymentController&action=index';</script>";
        exit();
    }

    public function exportExcel() {
        $model = $this->model("PaymentModel");

        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $payments = $model->getPayments($keyword);

        $filename = "Danh_Sach_Thanh_Toan_" . date('Ymd') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";

        echo '<table border="1">';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th>MaThanhToan</th>
                <th>MaDatPhong</th>
                <th>KhachHang</th>
                <th>SoPhong</th>
                <th>TienPhong</th>
                <th>TienDichVu</th>
                <th>TongTien</th>
                <th>PhuongThuc</th>
                <th>NgayThanhToan</th>
              </tr>';

        if (!empty($payments)) {
            foreach ($payments as $row) {
                echo '<tr>';
                echo '<td>' . ($row['MaThanhToan'] ?? '') . '</td>';
                echo '<td>' . ($row['MaDatPhong'] ?? '') . '</td>';
                echo '<td>' . ($row['KhachHang'] ?? '') . '</td>';
                echo '<td>' . ($row['SoPhong'] ?? '') . '</td>';
                echo '<td>' . ($row['TienPhong'] ?? '') . '</td>';
                echo '<td>' . ($row['TienDichVu'] ?? '') . '</td>';
                echo '<td>' . ($row['TongTien'] ?? '') . '</td>';
                echo '<td>' . ($row['PhuongThuc'] ?? '') . '</td>';
                echo '<td>' . ($row['NgayThanhToan'] ?? '') . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="9">Khong co du lieu tim thay</td></tr>';
        }
        echo '</table>';
        exit();
    }
}

