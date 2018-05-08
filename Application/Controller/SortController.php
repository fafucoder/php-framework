<?php
namespace Application\Controller;

use System\Controller;
use Application\Models\SortModel as Sort;

class SortController extends Controller {
    public function index(){
        return $this->display();
    }

    public function showCart($gid) {
        $Sort = new Sort();
        $data=$Sort->getSort($gid);
        $this->assign('sort', $data);
        return $this->display('sort/show');
    }
}