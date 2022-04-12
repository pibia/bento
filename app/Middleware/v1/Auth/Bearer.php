<?php

namespace App\Middleware\v1\Auth;

use Core\Http\{
    Input,
    Request,
    Response,
};

use Core\{
    Response\Api,
    Response\Json,
    Utilities\Util,
    Controller\Main,
};

use App\Models\v1\{
    User,
};

class bearer extends Main {

    public $user = null;
    private $_auth = null;
    private $_token = null;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index(){
        
        if(!empty($this->request->headers['Authorization'])){
            list($this->_auth, $this->_token) = explode(' ', $this->request->headers['Authorization']);
        }
        
        switch($this->_auth){
            case 'Bearer':
                
                $user = $this->user->getToken($this->_token);
                if(!$user){
                    Api::error(500, 'Error. Token not recognized.');
                } else {
                    return [
                        'auth' => [
                            'bearer' => [
                                'id' => $user['id'],
                                'username' => $user['username'],
                            ],
                        ],
                    ];
                }
                
                break;
            default:
                Api::error(500, 'Authorization: Bearer is not set.');
        }

    }

}