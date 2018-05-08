<?php 
namespace Application\Models;

class SortModel{
    public $conn;

    public function __construct(){
        $this->conn=self::conn();
    }

    // 获取特定商品分类
    public function getSort($gid){
        if ($this->conn) {
            $data=array();
            $query="select * from goods_info where pid='$gid'";
            $result=mysqli_query($this->conn,$query);
            while ($row=mysqli_fetch_assoc($result)) {
                $data[]=$row;
            }
            mysqli_free_result($result);
            return $data;
        }
    }

    // 获取商品信息信息
    public function getDetail($detail_id){
        if ($this->conn) {
            $data=array();
            $query="select goods_weight,total_weight,goods_jifen,goods_info,goods_name,goods_price,goods_made,goods_img from goods_info,goods_info_list where goods_info.detail_id=goods_info_list.detail_id and goods_info.detail_id=$detail_id and goods_info_list.detail_id=$detail_id limit 1";

            $result=mysqli_query($this->conn,$query);
            while ($row=mysqli_fetch_assoc($result)) {
                $data=$row;
            }
            mysqli_free_result($result);
            return $data;
        }
    }

    // 查找商品
    public function searchGoods($content){
        $ret=array();
        $query="select goods_name,detail_id from goods_info where goods_name like '%$content%'";
        $result=mysqli_query($this->conn,$query);
        while ($row=mysqli_fetch_assoc($result)) {
            $ret[]=$row;
        }
        return $ret;
    }

    public static function conn(){
        $conn=mysqli_connect("119.29.27.177","linrc","linrc","changeapp");
        if (!$conn) {
            die("连接失败!".mysqli_connect_errno());
            return false;
        }else{
            mysqli_query($conn,"SET NAMES UTF8");
            return $conn;
        }
    }

    public function __destruct(){
        mysqli_close($this->conn);
    }

}