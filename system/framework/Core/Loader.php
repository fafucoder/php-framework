<?php
namespace Framework\Core;

/**
 * Loader.
 *
 * Loader class main function is load class automatically based on some conventions
 *
 * @since 1.0.0
 */
class Loader {
    /**
     * Stores the include path.
     *
     * @var string
     */
    private $includePath = '';

    /**
     * Contains a cache map of previously registered namespaces.
     *
     * @var array
     */
    private $namespaces = array();

    /**
     * Contains a cache map of previously registered classes.
     *
     * @var array
     */
    private $classes = array();

    /**
     * Contains a cache map of previously registered bundles.
     *
     * @var array
     */
    private $bundles = array();

    /**
     * Contains a cache map of previously registered prefixes.
     *
     * @var array
     */
    private $prefixes = array();

    /**
     * Construct.
     * 
     * @param  string $include_path include root path
     * @return void                
     */
    public function __constuct($include_path = false) {

    }

    public function setIncludePath($include_path) {

    }

    public function getIncludePath() {

    }

    public function registerNamespaces($namespaces, $merge = true) {

    }

    public function getNamespaces() {

    }

    public function registerClasses($classes = array(), $merge = true) {

    }

    public function getClasses() {

    }

    public function registerBundles($bundles = array(), $merge = true) {

    }

    public function getBundles() {

    }

    public function registerPrefixes($prefixes = array(), $merge = true) {

    }

    public function getPrefixes() {

    }

    public function register() {

    }

    public function unregister() {

    }

}