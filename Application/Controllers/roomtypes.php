<?php
use MVC\Controller;
class ControllersRoomtypes extends Controller {
    public function index() { $this->send(200,['success'=>true,'data'=>$this->model('roomtypes')->getAll()]); }
    public function show($p) { $r=$this->model('roomtypes')->getById($p['id']); $r?$this->send(200,['success'=>true,'data'=>$r]):$this->send(404,['success'=>false,'message'=>'Not found']); }
    public function store() { $this->send(201,['success'=>true,'data'=>$this->model('roomtypes')->create($this->request->input())]); }
    public function update($p) { $this->model('roomtypes')->update($p['id'],$this->request->input()); $this->send(200,['success'=>true]); }
    public function destroy($p) { $this->model('roomtypes')->delete($p['id']); $this->send(200,['success'=>true]); }
}
