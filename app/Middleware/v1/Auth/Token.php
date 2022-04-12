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

class token extends Main {

    public $user = null;
    private $_token = null;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index(){

        if(!empty($this->request->headers['Token'])){
            $this->_token = $this->request->headers['Token'];
        } elseif(!empty($this->request->post['token'])){
            $this->_token = $this->request->post['token'];
        } else {
            Api::error(500, 'Authorization: Token is not set.');
        }
        
        $user = $this->user->getToken($this->_token);
        if(!$user){
            Api::error(500, 'Error. Token not recognized.');
        } else {
            return [
                'auth' => [
                    'token' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                    ],
                ],
            ];
        }
    }

}