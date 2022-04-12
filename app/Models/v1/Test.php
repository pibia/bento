<?php

namespace App\Models\v1;

use Core\{
    Utilities\Util,
    Model\Mysql,
    Model\Redis,
};

class test {

    public function __construct(Mysql $mysql, Redis $redis){
        $this->mysql = $mysql;
        $this->redis = $redis;
    }

    public function testGetAll(){
        return $this->mysql->table('test')
                    ->get();
    }

    public function testGet($id){
        return $this->mysql->table('test')
                    ->whereId($id)
                    ->debug()
                    ->get();
    }

    public function testCreate($data){
        return $this->mysql->table('test')
                    ->values($data)
                    ->insert();
    }

    public function testUpdate($data){
        $this->mysql->table('test')
                    ->values($data)
                    ->whereId($data['id'])
                    ->update();
        return $data['id'];
    }
}
