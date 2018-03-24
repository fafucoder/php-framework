<?php

namespace System;

class Container extends Injector {
    /**
     * Stores the _services.
     *
     * @var array
     */
    protected $_services = array();

    /**
     * Stores the shared service instance.
     *
     * @var array
     */
    protected $_instances = array();

    /**
     * Stores the _extends closure.
     *
     * @var array
     */
    protected $_extends = array();

    /**
     * Stores the _frozen service name.
     *
     * @var array
     */
    protected $_frozen = array();

    /**
     * Stores the _providers.
     *
     * @var array
     */
    protected $_providers = array();

    /**
     * Stores the registered _providers.
     *
     * @var array
     */
    protected $_registeredProviders = array();

    /**
     * Stores the properties of container.
     *
     * @var array
     */
    protected $_properties = array();

    /**
     * Construct.
     *
     * @param array $definitions
     * @param mixed $properties
     */
    public function __construct($properties = array()) {
        if (!empty($properties)) {
            $this->load($properties);
        }
    }

    /**
     * Load the config to set the properties of container.
     * @param array $properties
     */
    public function load($properties = array()) {
        foreach ($properties as $prop => $value) {
            $this->_properties[$prop] = $value;
        }
    }

    /**
     * Get or Set a propery.
     *
     * @param mixed      $id    Id of the property to get
     * @param mixed      $prop
     * @param null|mixed $value
     *
     * @return mixed
     */
    public function prop($prop, $value = null) {
        if (!is_string($prop)) {
            throw new \Exception('Container function "prop": The property name must be a string');
        }
        if (null === $value) {
            return isset($this->_properties[$prop]) ? $this->_properties[$prop] : null;
        }
        $this->_properties[$prop] = $value;
    }

    /**
     * Return whether a prop exists.
     *
     * @param string $prop
     *
     * @return bool
     */
    public function hasProp($prop) {
        return isset($this->_properties[$prop]);
    }

    /**
     * Add service to container.
     *
     * @param string $name       service name
     * @param array  $args       args passed to maker
     * @param mixed  $definition Service definition
     */
    public function set($name, $definition) {
        if (isset($this->_frozen[$name])) {
            throw new \RuntimeException(sprintf('Container function "set": Cannot override frozen service "%s".', $name));
        }

        $this->_services[$name] = $definition;
    }

    /**
     * Get the service instance with the given name.
     *
     * @param string $name service name
     * @param array  $args args passed to maker
     * @param bool   $new  whether return a new instance
     *
     * @return object service instance
     */
    public function get($name, array $args = array(), $new = false) {
        if (array_key_exists($name, $this->_services)) {
            if (isset($this->_instances[$name])) {
                return $this->_instances[$name];
            }

            $instance = $this->newInstance($name, $args);

            if (!$new) {
                $this->_instances[$name] = $instance;
            }

            $this->_frozen[$name] = true;

            return $instance;
        } elseif (array_key_exists($name, $this->_providers)) {
            $this->registerServiceProvider($name);

            return $this->get($name, $args, $new);
        }
        throw new \InvalidArgumentException(sprintf('Container function "get": The service "%s" is not defined.', $name));
    }

    /**
     * Generate a new service instance with the given name.
     *
     * @param string $name service name
     * @param array  $args args passed to maker
     *
     * @return object service instance
     */
    public function factory($name, array $args = array()) {
        return $this->get($name, $args, true);
    }

    /**
     * Instance a new service with the given name.
     *
     * @param string $name service name
     * @param array  $args args passed to maker
     *
     * @return object service instance
     */
    private function newInstance($name, array $args = array()) {
        if (isset($this->_services[$name])) {
            $definition = $this->_services[$name];

            $matched = false;

            if (is_callable($definition)) {
                $matched = true;

                $executable = new Executable($definition);
                $reflection = $executable->getReflection();

                $instance = $executable->invokeArgs(array_merge(array($this), $args));
            }

            if (!$matched && is_string($definition) && class_exists($definition)) {
                $matched = true;

                $instance = $this->make($definition, $args);
            }

            if (!$matched && is_object($definition)) {
                $matched = true;

                $instance = $definition;
            }

            if (!$matched) {
                throw new \Exception(sprintf('Container function "newInstance": Service "%s" definition is not valid.', $name));
            }

            if (!is_object($instance)) {
                throw new \Exception(sprintf('Container function "newInstance": Service "%s" did not result in an object.', $name));
            }

            if (array_key_exists($name, $this->_extends)) {
                foreach ($this->_extends[$name] as $callable) {
                    $instance = call_user_func($callable, $instance, $this);
                }
            }

            return $instance;
        }

        throw new \InvalidArgumentException(sprintf('Container function "newInstance": The service "%s" is not defined.', $name));
    }

    /**
     * Gets the service defining an object.
     *
     * @param string $name service name
     *
     * @return mixed the service defining an object
     */
    public function raw($name) {
        if (!isset($this->_services[$name])) {
            throw new \InvalidArgumentException(sprintf('Container function "raw": The service "%s" is not defined.', $name));
        }

        return $this->_services[$name];
    }

    /**
     * Extends an object definition.
     *
     * Useful when you want to extend an existing object definition,
     * without necessarily loading that object.
     *
     * @param string $name service name
     * @param $callable
     */
    public function extend($name, $callable) {
        if (!isset($this->_services[$name])) {
            throw new \InvalidArgumentException(sprintf('Container function "extend": The service "%s" is not defined.', $name));
        }

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Container function "extend": Extension service definition is not a Closure or invokable object.');
        }

        if (!array_key_exists($name, $this->_extends)) {
            $this->_extends[$name] = array();
        }

        $this->_extends[$name][] = $callable;

        if (isset($this->_instances[$name])) {
            $this->_instances[$name] = call_user_func($callable, $this->_instances[$name], $this);
        }
    }

    /**
     * Returns a value indicating whether the container can provide something for the given name.
     *
     * @param string $name service name
     *
     * @return bool
     */
    public function has($name) {
        if (array_key_exists($name, $this->_instances)) {
            return true;
        }

        if (array_key_exists($name, $this->_services)) {
            return true;
        }

        if (array_key_exists($name, $this->_providers)) {
            return true;
        }

        return false;
    }

    /**
     * Remove the definition with the given name.
     *
     * @param string $name service name
     */
    public function clear($name) {
        if ($this->has($name)) {
            unset($this->_services[$name], $this->_instances[$name], $this->_frozen[$name], $this->_extends[$name]);
        }
    }

    /**
     * Add Service Provider.
     *
     * @param string|class|mixed $provider
     */
    public function addServiceProvider($provider) {
        if (is_string($provider) && class_exists($provider)) {
            $provider = new $provider();
        }
        if ($provider instanceof ServiceProvider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot($this);
            }

            foreach ($provider->provides() as $service) {
                $this->_providers[$service] = $provider;
            }
        } else {
            throw new \InvalidArgumentException(
                sprintf('Container function "addServiceProvider": "%s" is not valid service provider class name or instance', $provider)
            );
        }
    }

    /**
     * Register Serive provider.
     *
     * @param string $service
     */
    public function registerServiceProvider($service) {
        if (!array_key_exists($service, $this->_providers)) {
            throw new \InvalidArgumentException(
                sprintf('Container function "registerServiceProvider": "%s" is not provided by a service provider', $service)
            );
        }

        $provider = $this->_providers[$service];

        // ensure that the provider hasn't already been invoked by any other service request
        if (in_array(get_class($provider), $this->_registeredProviders)) {
            return;
        }

        $provider->register($this);

        $this->_registeredProviders[] = get_class($provider);
    }

    /**
     * Magic Methods for get service instance by identifier.
     *
     * @param string $name service name
     *
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * Magic Methods for Add service to the container.
     *
     * @param string $name       service name
     * @param mixed  $definition Service definition
     *
     * @return mixed
     */
    public function __set($name, $definition) {
        return $this->set($name, $definition);
    }

    /**
     * Magic Methods for check if identifier is set or not.
     *
     * @param string $name service name
     *
     * @return bool
     */
    public function __isset($name) {
        return $this->has($name);
    }

    /**
     * Magic Methods for remove the definition by identifier.
     *
     * @param string $name service name
     */
    public function __unset($name) {
        $this->clear($name);
    }
}
