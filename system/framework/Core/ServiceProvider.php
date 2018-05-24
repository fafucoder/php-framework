<?php
namespace System\Core;

abstract class ServiceProvider {
    /**
     * @var array
     */
    protected $provides = array();

    /**
     * Returns a boolean if checking whether this provider provides a specific
     *
     * @param string     $service
     * @param null|mixed $alias
     *
     * @return bool|array
     */
    public function provides($alias = null) {
        if (null !== $alias) {
            return in_array($alias, $this->provides);
        }

        return $this->provides;
    }

    /**
     * Registers services on the given container.
     *
     * @param $container A container instance
     *
     * @return bool true
     */
    public function register($container) {
        return true;
    }
}
