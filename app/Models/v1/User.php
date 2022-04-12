<?php

namespace App\Models\v1;

use Core\{
    Utilities\Util,
};

use \Core\Model\{
    Mysql,
    Redis,
};

class user {

    private $mysql = null;
    private $redis = null;

    public function __construct(Mysql $mysql, Redis $redis){
        $this->mysql = $mysql;
        $this->redis = $redis;
    }

    public function userGetAll(){
        return $this->mysql->table('users')
                    ->get();
    }

    public function userGet($id){
        return $this->mysql->table('users')
                    ->whereId($id)
                    ->single()
                    ->get();
    }

    public function userCreate($data){
        
        return $this->mysql->table('users')
                    ->values($data)
                    ->insert();
    }

    public function userUpdate($data){
        
        $this->mysql->table('users')
                    ->values($data)
                    ->whereId($data['id'])
                    ->update();
        return $data['id'];
    }

    public function getToken($token){
        return $this->mysql->table('users')
                    ->where('token', '=', $token)
                    ->single()
                    ->get();
    }

    public function getUsername($username){
        return $this->mysql->table('users')
                    ->where('username', '=', $username)
                    ->single()
                    ->get();
    }
}
