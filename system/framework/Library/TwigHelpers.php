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
            new \Twig_SimpleFunction('image_exists', array($this, 'images_exists')),
        );
    }

    public function url($url = '', $vars = '', $suffix = true) {
        return Url::build($url, $vars, $suffix);
    }

    public function image_exists($img) {
        return file_exists($img);
    }
}
