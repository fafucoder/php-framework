<?php
namespace Application\Controller;

use System\Controller;
use System\Response;

class PersonalController extends Controller {

    public $beforeAction = array(
        'login' => array(),
    );

    public function index() {
        
    }

    public function login() {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $content = $this->display('user/login');
            $response = new Response($content);
            $response->send();
        }
    }
}