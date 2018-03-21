<?php

namespace System;

use Closure;
use ReflectionFunction;
use ReflectionMethod;

class Executable {
    /**
     * @var callable The callable.
     */
    private $callable = null;

    /**
     * @var \ReflectionFunction|ReflectionMethod
     */
    private $reflection = null;

    /**
     * The constructor.
     *
     * @param callable $callable A callable.
     */
    public function __construct(callable $callable) {
        $this->callable = $callable;
    }

    /**
     * Create Reflection.
     *
     * @return \ReflectionFunctionAbstract
     */
    public function getReflection() {
        if ($this->reflection) {
            return $this->reflection;
        }

        $callable = $this->callable;

        if ($this->isFunction()) {
            $this->reflection = new ReflectionFunction($callable);
        } elseif ($this->isClosure()) {
            if ($callable instanceof Closure) {
                // see https://bugs.php.net/bug.php?id=65432
                $this->reflection = new ReflectionFunction($callable);
            } else {
                $this->reflection = new ReflectionMethod($callable, '__invoke');
            }
        } elseif ($this->isClassMethod()) {
            if (is_string($callable) && strpos($callable, '::')) {
                $callable = explode('::', $callable);
            }

            if (method_exists($callable[0], $callable[1])) {
                $this->reflection = new ReflectionMethod($callable[0], $callable[1]);
            }
        } elseif ($this->isInstanceMethod()) {
            if (method_exists($callable[0], $callable[1])) {
                return new ReflectionMethod($callable[0], $callable[1]);
            }
        }

        return $this->reflection;
    }

    /**
     * Returns parameters of the callable.
     */
    public function getParameters() {
        $reflection = $this->getReflection();
        if ($reflection) {
            return $reflection->getParameters();
        }
    }

    /**
     * Invokes the callable with parameter(s).
     *
     * @return mixed The return value.
     */
    public function __invoke() {
        return $this->invokeArgs(func_get_args());
    }

    /**
     * Invokes the callable with parameter(s).
     *
     * @return mixed The return value.
     */
    public function invoke() {
        return $this->invokeArgs(func_get_args());
    }

    /**
     * Invokes the callable with an array of parameter(s).
     *
     * @param array $params The params to be passed to the callable.
     * @param array $args
     *
     * @return mixed The return value.
     */
    public function invokeArgs(array $args = array()) {
        $parameters = $this->getParameters();
        $params = array();

        if (is_array($parameters)) {
            foreach ($parameters as $i => $param) {
                if (isset($args[$param->name])) {
                    // a named param value is available
                    $params[] = $args[$param->name];
                } elseif (isset($args[$i])) {
                    // a positional param value is available
                    $params[] = $args[$i];
                } elseif ($param->isDefaultValueAvailable()) {
                    // use the default value
                    $params[] = $param->getDefaultValue();
                } else {
                    throw new Exception(sprintf('Executable function "invokeArgs": The param "%s" is not specified.', $param->name));
                }
            }
        }

        return call_user_func_array($this->callable, $params);
    }

    /**
     * Returns the raw callable.
     *
     * @return callable The callable.
     */
    public function getCallable() {
        return $this->callable;
    }

    /**
     * Returns whether the callable is a closure.
     *
     * @return bool True if it is a Closure, false otherwise.
     */
    public function isClosure() {
        return is_object($this->callable);
    }

    /**
     * Returns whether the callable is a function.
     *
     * @return bool True if it is a function, false otherwise.
     */
    public function isFunction() {
        return is_string($this->callable) && strpos($this->callable, '::') === false;
    }

    /**
     * Returns whether the callable is a class method.
     *
     * @return bool True if it is a class method, false otherwise.
     */
    public function isClassMethod() {
        if (is_string($this->callable) && strpos($this->callable, '::') !== false) {
            $callable = explode('::', $this->callable);

            return is_array($callable) && is_string($callable[0]);
        }

        return is_array($this->callable) && is_string($this->callable[0]);
    }

    /**
     * Returns whether the callable is an instance method.
     *
     * @return bool True if it is an instance method, false otherwise.
     */
    public function isInstanceMethod() {
        return is_array($this->callable) && is_object($this->callable[0]);
    }
}
