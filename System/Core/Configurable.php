<?php
/*
 * This file is part of the Amazing Framework
 * Copyright (c) AmazingSurge.
 */
namespace Amazing\Common\Core;

/**
 * Configurable.
 *
 * Configurable is the abstract class that should be implemented by classes who support configuring
 * its properties through the last parameter to its constructor.
 *
 * Classes extends this classs must declare their constructors accept a configuration array with the last parameter.
 *
 * The class is based on Yii2 implimentation, BSD license (https://github.com/yiisoft/yii2/blob/master/framework/LICENSE.md).
 *
 * @since 1.0.0
 */
abstract class Configurable {
    /**
     * Constructor.
     * Initializes the object with the given configuration `$config`.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct(array $config = array()) {
        static::configure($this, $config);
    }

    /**
     * Configures an object with the initial property values.
     *
     * @param object $object     the object to be configured
     * @param array  $properties the property initial values given in terms of name-value pairs.
     *
     * @return object the object itself
     */
    public static function configure($object, $properties) {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }
}
