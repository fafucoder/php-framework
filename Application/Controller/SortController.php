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

    public function detail() {
        $did = isset($_GET['did']) ? $_GET['did']: "";
        $Sort = new Sort();
        $data=$Sort->getDetail($did);
        $this->assign('detail', $data);
        $this->assign('detail_id', $did);
        return $this->display('sort/detail');
    }
}