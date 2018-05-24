<?php
namespace Application\Controller;

use System\Controller;

class UserController extends Controller {
    public function login() {
        return $this->display();
    }
    
    public function sign() {
        return $this->display();
    }
}