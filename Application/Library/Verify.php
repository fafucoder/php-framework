<?php
namespace Application\Library;

class Verify {
    private $width; //宽
    private $height; //高
    private $num;  //数量
    private $code; //验证码
    private $img;  //图像的资源
    

    function __construct($width=80, $height=40, $num=4) {
       $this->width = $width;
       $this->height = $height;
       $this->num = $num;
       $this->code =$this->createcode(); //调用自己的方法
    }
    
    public function getcode() {
        return $this->code;
    }
    
    //输出图像
    public function  outimg() {
       $this->createBg();
      
       $this->outString();
      
       $this->setPix();
       $this->printImg();
    }
    
    //创建背景
    private function createBg() {
       //创建资源
       $this->img = imagecreatetruecolor($this->width, $this->height);
       // 背景
       $bgcolor = imagecolorallocate($this->img,255,255,255); 
       imagefill($this->img, 0, 0, $bgcolor);
       $bordercolor = imagecolorallocate($this->img, 255, 255, 255);
       imagerectangle($this->img, 0, 0, $this->width-1, $this->height-1, $bordercolor);
    }
    
    //画字
    private function outString() {
        for($i=0; $i<$this->num; $i++) {
            $color= imagecolorallocate($this->img, rand(0, 128), rand(0, 128), rand(0, 128)); 
            $fontsize=5; //字体大小 
            $x =($this->width/$this->num)*$i+rand(3,5); //水平位置
            $y = rand(0, imagefontheight($fontsize));
            imagechar($this->img, $fontsize, $x, $y, $this->code{$i}, $color); //画出每个字符
        }
    }
    
    //设置干扰元素
    private function setPix() {
        //加上点数
        for($i=0; $i<50; $i++) {
            $color= imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)); 
            imagesetpixel($this->img, rand(1, $this->width-2), rand(1, $this->height-2), $color);
        }
    }
    
    //输出图像
    private function printImg() {
        header("Content-type: image/png");
        imagegif($this->img); 
    }
    
    //生成验证码字符串
    private function createCode() {
        $codes = "3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY";
        $code = "";
        for($i=0; $i < $this->num; $i++) {
            $code .=$codes{rand(0, strlen($codes)-1)}; 
        }
        return $code;
    }
    
    public function destroyImg(){
        imagedestroy($this->img);
    }
    // // 用于自动销毁图像资源
    // public function __destruct() {
    //     imagedestroy($this->img);
    // }

    
}