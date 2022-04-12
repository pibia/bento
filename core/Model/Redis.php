<?php

namespace Core\Model;

use Core\{
    Utilities\Util,
    Database\Redis as Db,
};

class Redis {

    private $db = null;

    public function construct(){
        $this->db = new Db();
    }

}
