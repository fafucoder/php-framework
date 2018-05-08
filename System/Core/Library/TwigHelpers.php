<?php 
namespace System\Library;

use System\Url;

class TwigHelpers extends \Twig_Extension {

    public function getName() {
        return "twig_helpers";
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'url')),
        );
    }

    public function url($url = '', $vars = '', $suffix = true) {
        return Url::build($url, $vars, $suffix);
    }
}
