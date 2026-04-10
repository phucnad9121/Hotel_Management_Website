<?php
use MVC\Controller;
class ControllersPayments extends Controller {
    public function index() { $this->send(200,['success'=>true,'data'=>$this->model('payments')->getAll()]); }
    public function show($p) { $r=$this->model('payments')->getById($p['id']); $r?$this->send(200,['success'=>true,'data'=>$r]):$this->send(404,['success'=>false,'message'=>'Not found']); }
    public function store() { $this->send(201,['success'=>true,'data'=>$this->model('payments')->create($this->request->input())]); }
    public function byBooking($p) { $id=$p['booking_id']??''; $this->send(200,['success'=>true,'data'=>$this->model('payments')->getByBooking($id)]); }
}
