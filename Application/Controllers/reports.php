<?php
use MVC\Controller;
class ControllersReports extends Controller {
    public function dashboard() {
        $rooms = $this->db->query("SELECT COUNT(*) as total FROM rooms_room")->row['total'];
        $occupied = $this->db->query("SELECT COUNT(*) as total FROM rooms_room WHERE KhaDung='No'")->row['total'];
        $this->send(200,['success'=>true,'data'=>['rooms'=>['total'=>$rooms,'occupied'=>$occupied,'available'=>$rooms-$occupied]]]);
    }
    public function revenue() { $this->send(200,['success'=>true,'data'=>['total'=>0]]); }
    public function occupancy() { $this->send(200,['success'=>true,'data'=>['rate'=>0]]); }
    private $db;
    public function __construct() { parent::__construct(); $m = $this->model('auth'); $this->db = $m->db; }
}
