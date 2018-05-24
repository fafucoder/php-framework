<?php
namespace Application\Controller;

use Application\Library\Verify;

class VerifyController {
    public function code() {
        $vcode=new Verify();
        $code=$vcode->outimg();
        $_SESSION['code']=$vcode->getcode();
        
    }
}