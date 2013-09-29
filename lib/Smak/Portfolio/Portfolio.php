<?php

namespace Smak\Portfolio;

use Symfony\Component\Finder\Finder;

/**
 * Portfolio.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class Portfolio extends Finder implements \Countable
{
    /**
     * Object datas
     */
    protected $data = array();

    /**
     * Gets all items
     *
     * @abstract
     */
    abstract public function getAll();

    /**
     * Unserialization helper
     *
     * @abstract
     */
    abstract public function __wakeup();

    /**
     * Returns saved properties for serialization
     *
     * @return array
     */
    public function __sleep()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * Object data setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Object data getter
     *
     * @param string $name
     * @return mixed | null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Object data presence tester
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Object data remover
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Mandatory count method
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }

    /**
     * Gets an item by its index position
     *
     * @param int $index
     * @return \Smak\Portfolio\(Set|Photo)
     * @throws InvalidArgumentException, OutOfRangeException
     */
    public function getById($index)
    {
        if (!is_int($index) || 0 > $index) {
            throw new \InvalidArgumentException('Item index argument must an integer >=  0!');
        }
        
        if (!$this->getAll()->offsetExists($index)) {
            throw new \OutOfRangeException(sprintf('Item index #%d does not exist!', $index));
        }

        return $this->getAll()->offsetGet($index);
    }

    /**
     * Gets an item by its name
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function getByName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('Item name argument must a non empty string!');
        }
    }

    /**
     * Gets the first item of the itetator
     * 
     * @return Smak\Portfolio\(Set|Photo)
     */
    public function getFirst()
    {
        $iterator = iterator_to_array($this->getAll());
        
        return array_shift($iterator);
    }
    
    /**
     * Gets the last item of the itetator
     * 
     * @return Smak\Portfolio\(Set|Photo)
     */
    public function getLast()
    {
        $iterator = iterator_to_array($this->getAll());
        
        return array_pop($iterator);
    }
}