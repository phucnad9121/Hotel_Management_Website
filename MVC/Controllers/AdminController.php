<?php
class AdminController extends controller {
    
    // 1. Trang chủ Admin (Banner chào mừng)
    public function index() {
        ob_start();
        $this->view("Pages/Admin"); 
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "dashboard"]);
    }


}