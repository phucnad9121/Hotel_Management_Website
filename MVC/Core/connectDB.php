<?php
class connectDB {
    protected $con;

    public function __construct() {
        // $this->con = mysqli_connect(
        //     "localhost",
        //     "root",
        //     "",
        //     "web_hotel_mngt"   // TÊN DATABASE
        // );
        $host = "127.0.0.1";       
        $user = "root";
        $pass = "";
        $db   = "web_hotel_mngt";   // TÊN DATABASE
        $port = 3306;               // CỔNG MySQL CỦA BẠN

        $this->con = mysqli_connect($host, $user, $pass, $db, $port);

        if (!$this->con) {
            die("Kết nối DB thất bại!");
        }

        mysqli_set_charset($this->con, "utf8");
    }

    public function select($sql) {
        $result = mysqli_query($this->con, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function selectOne($sql) {
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function execute($sql) {
        return mysqli_query($this->con, $sql);
    }
}
