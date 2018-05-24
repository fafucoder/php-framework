<?php
namespace Application\Controller;

use System\Controller;

class CartController extends Controller {
    public function index(){
        $Cart = GoodsController::getCart();
        $carts = array(
            'cart_list' => $Cart->ItemList(),
            'cart_num' => $Cart->ItemTotal(),
            'total_price' => $Cart->TotalPrice(),
        );
        if (isset($_SESSION['cart']) && !empty($carts['cart_list']) && $carts['cart_num'] > 0) {
            $this->assign($carts);
            return $this->display();
        } else {
            return $this->display('cart/empty');
        }
        
    }
}