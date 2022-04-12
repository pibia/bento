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

class basic extends Main {

    public $user = null;
    private $_username = null;
    private $_password = null;
    private $_auth = null;
    private $_token = null;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index(){
        if(!empty($this->request->headers['Authorization'])){
            list($this->_auth, $this->_token) = explode(' ', $this->request->headers['Authorization']);
        } elseif(empty($this->request->http['auth']['user'])){
            Api::error(500, 'Authorization: Username is not set.');
        } elseif(empty($this->request->http['auth']['password'])){
            Api::error(500, 'Authorization: Password is not set.');
        } else {
            $this->_username = $this->request->http['auth']['user'];
            $this->_password = $this->request->http['auth']['password'];
        }
        
        if($this->_auth=='Basic'){
            list($this->_username, $this->_password) = explode(':', base64_decode($this->_token));
        }
        
        $user = $this->user->getUsername($this->_username);
        if(!$user){
            Api::error(500, 'Error. User_ or password not recognized.');
        } elseif(!password_verify($this->_password, $user['password'])) {
            Api::error(500, 'Error. User or password_ not recognized.');
        } else {

            return [
                'auth' => [
                    'basic' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                    ],
                ]
            ];
        }
    }

}