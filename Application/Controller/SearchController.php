<?php
namespace Application\Controller;

use System\Controller;

class SearchController extends Controller {

    public function index(){
        return $this->show();
    }
}