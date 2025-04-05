<?php
/**
 * BRMedia DI Container Class
 *
 * Provides a dependency injection container for managing class dependencies.
 *
 * @package BRMedia\Includes\Core
 */

namespace BRMedia\Includes\Core;

class BRMedia_DI_Container {
    /** @var array Registered services */
    private $services = [];

    /** @var array Instantiated services */
    private $instances = [];

    /**
     * Registers a service
     *
     * @param string $key Service key
     * @param string $class Class name
     */
    public function register($key, $class) {
        $this->services[$key] = $class;
    }

    /**
     * Retrieves or creates a service instance
     *
     * @param string $key Service key
     * @return mixed Service instance
     * @throws Exception If service not found
     */
    public function get($key) {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->services[$key])) {
            throw new \Exception("Service '$key' not registered.");
        }

        $class = $this->services[$key];
        $this->instances[$key] = new $class($this); // Pass container for dependency injection
        return $this->instances[$key];
    }
}