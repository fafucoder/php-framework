<?php
namespace System\Core;

abstract class Single {
    /**
     * @var array
     */
    private static $_instances = array();

    /**
     * Returns the only instance of the Singleton class.
     *
     * @return $this
     */
    public static function getInstance() {
        $self = get_called_class();

        if (!isset(self::$_instances[$self])) {
            self::$_instances[$self] = new $self();
        }

        return self::$_instances[$self];
    }

    /**
     * Returns whether has Instance.
     *
     * @return bool
     */
    public static function hasInstance() {
        $self = get_called_class();

        return isset(self::$_instances[$self]);
    }
}
