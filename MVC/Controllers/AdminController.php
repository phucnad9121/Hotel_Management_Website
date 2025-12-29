<?php
class AdminController extends controller {
    
    // 1. Trang chủ Admin (Banner chào mừng)
    public function index() {
        ob_start();
        $this->view("Pages/Admin"); // Đây là file chứa cái sidebar và banner bạn gửi ở trên
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "dashboard"]);
    }

    // 2. Trang Quản lý bộ phận (Giao diện có Form và Bảng)
    public function department() {
        $model = $this->model("DepartmentModel");
        
        // Xử lý tìm kiếm nếu có
        if (isset($_POST['search'])) {
            $departments = $model->search($_POST['keyword']);
        } else {
            $departments = $model->getAll();
        }

        ob_start();
        // Gọi file view quản lý bộ phận
        $this->view("Pages/Department", ["departments" => $departments]); 
        $content = ob_get_clean();

        $this->view("Master", ["content" => $content, "page_tab" => "department"]);
    }

}