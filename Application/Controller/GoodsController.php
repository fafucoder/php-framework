<?php 
namespace Application\Controller;

class GoodsController {
    private static $ins=null; //实例变量
    protected $item=array(); //放商品的容器

    // 禁止外部引用
    final protected function __construct(){
    }
    // 禁止外部克隆
    final protected function __clone(){
    }

    // 类内部实例化
    protected static function getIns(){
        if (self::$ins instanceof self) {
            return self::$ins;
        }
        self::$ins=new self();
        return self::$ins;
    }

    // 商品保存到session中
    public static function getCart(){
        if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof self)) {
            $_SESSION['cart']=self::getIns();
        }
        return $_SESSION['cart'];
    }

    // 检测购物出中是否有该商品
    public function InItem($gid){
        if ($this->ItemType()==0) {
            return false;
        }
        if (!array_key_exists($gid, $this->item)) {
            return false;
        }else{
            return $this->item[$gid]['num'];
        }
    }

    // 一共有多少种商品
    public function ItemType(){
        return count($this->item);
    }

    // 一共有多少个商品
    public function ItemTotal(){
        $num=0;
        if ($this->ItemType() ==0) {
            $num=0;
        }
        foreach ($this->item as $value) {
            $num += $value['num'];
        }
        return $num;
    }

    // 添加商品
    public function AddItem($gid,$name,$price,$img,$num){
        if ($this->InItem($gid) !=false) {
            $this->item[$gid]['num']+=$num;
        }else{
            $this->item[$gid]=array();
            $this->item[$gid]['name']=$name;
            $this->item[$gid]['price']=$price;
            $this->item[$gid]['num']=$num;
            $this->item[$gid]['gid']=$gid;
            $this->item[$gid]['img']=$img;
        }
    }

    // 删除一个商品
    public function DelItem($gid){
        if ($this->InItem($gid) != false) {
            unset($this->item[$gid]);
        }
    }

    // 清空购物车
    public function EmptyCart(){
        $this->item=array();
    }

    // 商品数量减一
    public function ReduceOneItem($gid){
        if ($this->InItem($gid) !=false && $this->InItem($gid) > 1) {
            $this->item[$gid]['num']-=1;
        }
    }

    // 商品数量加一
    public function AddOneItem($gid){
        if ($this->InItem($gid) !=false) {
            $this->item[$gid]['num']+=1;
        }
    }

    // 计算单个商品的价格
    public function OnePrice($gid){
        if ($this->InItem($gid) !=false) {
            $price=$this->item[$gid]['price'];
            $num=$this->item[$gid]['num'];
            $oneprice=$price * $num;
            return $oneprice;
        }
    }

    // 计算总价格
     public function TotalPrice(){
        $price = 0.0;
        if($this->ItemType() == 0){
            return 0.0;
        }
        foreach($this->item as $v){
            // 提取price中的数值
            preg_match_all('/\d+\.\d+/',$v['price'],$arr);
            $price += $v['num']*(float)$arr[0][0];
        }
        return $price;
    }

    // 返回购买商品列表
    public function ItemList(){
        return $this->item;
    }

}