<?php
namespace System\Core;

abstract class Configurable {
    public function __construct(array $config = array()) {
        static::configurable($this, $config);
    }

    public static function configurable($object, $properties) {
        foreach ($properties as $name => $value) {
            $bbject->$name = $value;
        }
        return $object;
    }
}
