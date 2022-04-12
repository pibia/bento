<?php

namespace App\Controllers\v1;

use Core\{
    Response\Json,
    Response\Api,
    Utilities\Util,
    Controller\Main,
};

class user extends Main {

    public function all(){
        
        Json::set($this->model->userGetAll())->send();
    }

    public function view($id = null){

        if(!$id){
            $this->all();
        } else {
            Json::set($this->model->userGet($id))->send();
        }

    }

    public function create($id = null, $username = null, $password = null){

        $password = password_hash($password, PASSWORD_ARGON2ID);

        if(!is_null($id)){
            
            Json::set(['id' => $this->model->userUpdate([
                'id' => $id,
                'username' => $username,
                'password' => $password,
            ])])->header(201)->send();
        } else {

            $token = array_chunk(str_split(bin2hex(random_bytes(24))), 10);
            $token = implode('-', array_map(function ($e) {
                return implode('', $e);
            }, $token));
            
            Json::set(['id' => $this->model->userCreate([
                'username' => $username,
                'password' => $password,
                'token' => strtoupper($token),
            ])])->header(201)->send();
        }
        
    }
}