<?php
namespace System;

class Injector {
    /**
     * Stores the definitions.
     *
     * @var array
     */
    protected $_definitions = array();

    /**
     * Stores the shares classnames.
     *
     * @var array
     */
    protected $_shares = array();

    /**
     * Define instantiation directives for the specified class.
     *
     * @param string $name       The class (or alias) whose constructor arguments we wish to define
     * @param array  $args       An array mapping parameter names to values/instructions
     * @param mixed  $definition
     */
    public function define($name, $definition = array()) {
        if (empty($name) || !is_string($name)) {
            throw new \Exception('Injector function "define": Invalid define, the name for class should be non-empty string.');
        }

        $normalizedName = $this->normalizeName($name);

        if (isset($this->_shares[$normalizedName])) {
            throw new \Exception(sprintf('Injector function "define": Cannot define class "%s" because it is currently shared.', $name));
        }

        if (is_object($definition) && $definition instanceof $name) {
            $this->_shares[$normalizedName] = $definition;
            $this->_definitions[$normalizedName] = null;
        } else {
            $this->_definitions[$normalizedName] = $definition;
        }
    }

    /**
     * Share the specified class/instance across the Injector context.
     *
     * @param mixed $nameOrInstance The class or object to share
     *
     * @throws ConfigException if $nameOrInstance is not a string or an object
     */
    public function share($nameOrInstance) {
        if (is_string($nameOrInstance)) {
            $this->shareClass($nameOrInstance);
        } elseif (is_object($nameOrInstance)) {
            $this->shareInstance($nameOrInstance);
        } else {
            throw new \Exception(sprintf('Injector function "share": Invalid share, requires a string class name or object instance; "%s" specified.', gettype($nameOrInstance)));
        }
    }

    /**
     * @param string $classname
     */
    private function shareClass($classname) {
        $normalizedName = $this->normalizeName($classname);
        // Set to null will instance the class when make
        $this->_shares[$normalizedName] = isset($this->_shares[$normalizedName])
        ? $this->_shares[$normalizedName]
        : null;
    }

    /**
     * @param class|mxied $obj
     *
     * @return string
     */
    private function shareInstance($obj) {
        $normalizedName = $this->normalizeName(get_class($obj));
        $this->_shares[$normalizedName] = $obj;
    }

    /**
     * @param string $classname
     * @param mixed  $className
     *
     * @return string
     */
    private function normalizeName($className) {
        return ltrim($className, '\\');
    }

    /**
     * Instantiate/provision a class instance.
     *
     * @param string $name
     * @param array  $args
     *
     * @throws InjectionException if a cyclic gets detected when provisioning
     *
     * @return mixed
     */
    public function make($name, array $args = array()) {
        $normalizedName = $this->normalizeName($name);

        // isset() is used specifically here because classes may be marked as "shared" before an
        // instance is stored. In these cases the class is "shared," but it has a null value and
        // instantiation is needed.
        if (isset($this->_shares[$normalizedName])) {
            return $this->_shares[$normalizedName];
        }

        $matched = false;

        if (isset($this->_definitions[$normalizedName])) {
            $definition = $this->_definitions[$normalizedName];

            if (is_callable($definition)) {
                $matched = true;

                $executable = new Executable($definition);
                $reflection = $executable->getReflection();

                $args = $this->provisionFuncArgs($reflection, $args);

                $obj = $executable->invokeArgs($args);
            } elseif (is_string($definition) && class_exists($definition)) {
                $matched = true;

                $obj = $this->make($definition, $args);
            } elseif (!is_object($definition)) {
                $matched = true;

                if (is_array($definition)) {
                    $args = array_replace($definition, $args);
                }

                $obj = $this->provisionInstance($name, $args);
            }
        } else {
            $matched = true;
            $obj = $this->provisionInstance($name, $args);
        }

        if (!$matched) {
            throw new InjectorException(sprintf('Injector function "make": Class "%s" definition is not valid.', $name));
        }

        if (!is_object($obj)) {
            throw new InjectorException(sprintf('Injector function "make": Making "%s" did not result in an object.', $name));
        }

        if (array_key_exists($normalizedName, $this->_shares)) {
            $this->_shares[$normalizedName] = $obj;
        }

        return $obj;
    }

    /**
     * @param string $className
     * @param array  $args
     */
    private function provisionInstance($className, array $args) {
        try {
            $reflectionClass = new \ReflectionClass($className);

            $constructor = $reflectionClass->getConstructor();

            if (!$constructor) {
                if (!$reflectionClass->isInstantiable()) {
                    $type = $reflectionClass->isInterface() ? 'interface' : 'abstract class';
                    throw new InjectorException(sprintf('Injector function "provisionInstance": The %s "%s" is not defined.', $type, $className));
                }
                $obj = new $className();
            } elseif (!$constructor->isPublic()) {
                throw new \Exception(sprintf('Injector function "provisionInstance": Cannot instantiate protected/private constructor in class %s', $className));
            } else {
                $args = $this->provisionFuncArgs($constructor, $args);

                $obj = $reflectionClass->newInstanceArgs($args);
            }

            return $obj;
        } catch (\ReflectionException $e) {
            throw new \Exception(sprintf('Injector function "provisionInstance": Could not make %s: %s";', $className, $e->getMessage()));
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $reflectionFunc
     * @param array                       $definition
     *
     * @var array $args
     *
     * @return array
     */
    private function provisionFuncArgs(\ReflectionFunctionAbstract $reflectionFunc, array $definition) {
        $args = array();

        $params = $reflectionFunc->getParameters();

        foreach ($params as $i => $param) {
            $name = $param->name;

            if (isset($definition[$i]) || array_key_exists($i, $definition)) {
                // indexed arguments take precedence over named parameters
                $arg = $definition[$i];
            } elseif (isset($definition[$name]) || array_key_exists($name, $definition)) {
                // interpret the param as a class name to be instantiated
                $arg = $this->make($definition[$name]);
            } elseif (($prefix = ':' . $name) && (isset($definition[$prefix]) || array_key_exists($prefix, $definition))) {
                // interpret the param as a raw value to be injected
                $arg = $definition[$prefix];
            } elseif (($prefix = '+' . $name) && isset($definition[$prefix])) {
                // build the param from callable
                if (!is_callable($definition[$prefix])) {
                    throw new \Exception(sprintf('Injector function "provisionFuncArgs": Param "%s" definition is not callable', $name));
                }
                $arg = call_user_func($definition[$prefix], $name, $this);
            } elseif ($reflectionClass = $param->getClass()) {
                // build the arg from type hinted class
                $className = $reflectionClass->getName();

                if ($param->isDefaultValueAvailable()) {
                    if (isset($this->_definitions[$className]) || isset($this->_shares[$className])) {
                        $arg = $this->make($className);
                    } else {
                        $arg = $param->getDefaultValue();
                    }
                } else {
                    $arg = $this->make($className);
                }
            } elseif ($param->isDefaultValueAvailable()) {
                $arg = $param->getDefaultValue();
            } else {
                $func = $param->getDeclaringFunction();
                if ($func instanceof \ReflectionMethod) {
                    $classWord = $func->getDeclaringClass()->name . '::';
                } else {
                    $classWord = '';
                }
                $funcWord = $classWord . $func->name;

                throw new \Exception(sprintf('Injector function "provisionFuncArgs": No definition available to provision typeless parameter $%s at position %d in %s().', $name, $param->getPosition(), $funcWord));
            }

            $args[] = $arg;
        }

        return $args;
    }

    /**
     * Returns a value indicating whether the specified class is defined.
     *
     * @param string $name The class name
     *
     * @return bool
     */
    public function isDefined($name) {
        $normalizedName = $this->normalizeName($name);

        return array_key_exists($normalizedName, $this->_definitions);
    }

    /**
     * Returns a value indicating whether the specified class is shared.
     *
     * @param string $name The class name
     *
     * @return bool
     */
    public function isShared($name) {
        $normalizedName = $this->normalizeName($name);

        return array_key_exists($normalizedName, $this->_shares);
    }
}
