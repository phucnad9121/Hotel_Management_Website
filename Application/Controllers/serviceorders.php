<?php
use MVC\Controller;
class ControllersServiceorders extends Controller {
    public function index($p) { $bid=$p['booking_id']??''; $this->send(200,['success'=>true,'data'=>$this->model('serviceorders')->getByBooking($bid)]); }
    public function store($p) { $bid=$p['booking_id']??''; $data=$this->request->input(); $this->send(201,['success'=>true,'data'=>$this->model('serviceorders')->add($bid,$data)]); }
    public function destroy($p) { $this->model('serviceorders')->remove($p['service_id']??''); $this->send(200,['success'=>true]); }
}
