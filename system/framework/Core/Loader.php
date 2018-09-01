<?php
namespace Framework\Core;

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
     * Contains a extend directory.
     * 
     * @var array
     */
    private $extends = array();

    /**
     * Contains loaded class.
     * 
     * @var array
     */
    private $loaded = array();

    /**
     * Register autoload file.
     * 
     * @var array
     */
    private $autoload = array();

    /**
     * Construct.
     * 
     * @param  string $include_path include root path
     * @return void                
     */
    public function __construct($include_path = false) {
        if ($include_path) {
            $this->includePath = $this->trailingslashit($include_path);
        }
    }

    /**
     * Set include path.
     * 
     * @param string $include_path 
     */
    public function setIncludePath($include_path = false) {
        if ($include_path) {
            $this->includePath = $this->trailingslashit($include_path);
        }
    }

    /**
     * Get include path.
     * 
     * @return string
     */
    public function getIncludePath() {
        return $this->includePath;
    }

    /**
     * Register namespace.
     * 
     * @param  array  $namespaces
     * @param  boolean $merge    
     * @return void
     */
    public function registerNamespaces(array $namespaces, $merge = true) {
        $namespaces = array_map(array($this, 'trailingslashit'), $namespaces);
        if ($merge) {
            $this->namespaces = array_merge($this->namespaces, $namespaces);
        } else {
            $this->namespaces = $namespaces;
        }
    }

    /**
     * Get registered namespace.
     * 
     * @return array 
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Register classes.
     * 
     * @param  array   $classes
     * @param  boolean $merge
     * @return void
     */
    public function registerClasses($classes = array(), $merge = true) {
        if ($merge) {
            $this->classes = array_merge($this->classes, $classes);
        } else {
            $this->classes = $classes;
        }
    }

    /**
     * Get registered classes.
     * 
     * @return array 
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * Register bundle to bind directory.
     * 
     * @param  array   $bundles 
     * @param  boolean $merge  
     * @return void
     */
    public function registerBundles(array $bundles = array(), $merge = true) {
        foreach ($bundles as $key => $bundle) {
            if (!isset($bundle['dir'])) {
                $bundles[$key]['dir'] = '';
            } else {
                $bundles[$key]['dir'] = $this->trailingslashit($bundle['dir']);
            }

            if (isset($bundle['root'])) {
                $bundles[$key]['root'] = false;
            }
        }

        if ($merge) {
            $this->bundles = array_merge($this->bundles, $bundles);
        } else {
            $this->bundles = $bundles;
        }
    }

    /**
     * Get registered bundle bind directory.
     * 
     * @return array
     */
    public function getBundles() {
        return $this->bundles;
    }

    /**
     * Return loaded class.
     * 
     * @return array 
     */
    public function getLoaded() {
        return $this->loaded;
    }

    /**
     * Register class prefixes.
     * 
     * @param  array   $prefixes 
     * @param  boolean $merge
     * @return void
     */
    public function registerPrefixes(array $prefixes = array(), $merge = true) {
        $prefixes = array_map(array($this, 'trailingslashit'), $prefixes);
        if ($merge) {
            $this->prefixes = array_merge($this->prefixes, $prefixes);
        } else {
            $this->prefixes = $prefixes;
        }
    }

    /**
     * Get registered class prefixes.
     * 
     * @return array
     */
    public function getPrefixes() {
        return $this->prefixes;
    }

    /**
     * Register extends directory an in this directory class can be find.
     * 
     * @param  array   $extends 
     * @param  boolean $merge   
     * @return void
     */
    public function registerExtends(array $extends = array(), $merge = true) {
        $extends = array_merge(array($this, 'trailingslashit'), $extends);
        if ($merge) {
            $this->extends = array_merge($this->extends, $extends);
        } else {
            $this->extends = $extends;
        }
    }

    /**
     * Get register extends directory.
     * 
     * @return array 
     */
    public function getExtends() {
        return $this->extends;
    }

    /**
     * Register autoload file.
     * 
     * @param  array   $autolod 
     * @param  boolean $merge   
     * @return void           
     */
    public function registerAutoload(array $autoload = array(), $merge = true) {
        if ($merge) {
            $this->autoload = array_merge($this->autoload, $autoload);
        } else  {
            $this->autoload = $autoload;
        }
    }

    /**
     * Get autload files.
     * 
     * @return array 
     */
    public function getAutoload() {
        return $this->autoload;
    }

    /**
     * Register autoloader.
     * 
     * @param bool $prepend True to prepend to the autoload stack.
     * @return void
     */
    public function register($prepend = false) {
        spl_autoload_register(array($this, 'autoload'), true, (bool) $prepend);

        //require autoload file
        foreach ($this->autload as $file) {
            $this->requireFile($file);
        }
    }

    /**
     * Unregister autoloader.
     * 
     * @return void 
     */
    public function unregister() {
        spl_autoload_unregister(array($this, 'autoload'));
    }

    /**
     * Loads class file for a given class name.
     * 
     * @param  string $class
     * @return mixed  if success return file name, if error return false
     */
    public function autoload($class) {
        if ($file = $this->getFilePath($class)) {
            $this->requireFile($file);
            $this->loaded[$class] = $file;

            return true;
        }

        return false;
    }

    /**
     * Find the path to the file where the class is defined.
     * 
     * @param  string $class the name of the class.
     * @return string|null
     */
    public function getFilePath($class) {

    }

    /**
     * require file if path exists.
     * 
     * @param  string $path
     * @return boolean       
     */
    private function requireFile($file) {
        if ($this->fileExists($file)) {
            $file = $this->getFilePath . $file;
            require_once $file;
            return true;
        }
        return false;
    }

    /**
     * Appends a trailing slash.
     * 
     * @param  string $string
     * @return string
     */
    private function trailingslashit($string) {
        return rtrim($string, '/\\') . '/';
    }

    /**
     * Return if file exists.
     * 
     * @param  string $file file path
     * @return boolean       
     */
    private function fileExists($file) {
        return file_exists($this->includePath . $file);
    }
}