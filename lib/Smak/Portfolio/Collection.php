<?php

namespace Smak\Portfolio;

/**
 * Collection.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Collection extends Portfolio
{
    /**
     * Collection technical info (string)
     */
    protected $_dir;

    /**
     * Class constructor
     * 
     * @param string $dir Collection parent directory
     * @param int $depth The Finder depth to parse
     */
    public function __construct($dir = __DIR__)
    {
        parent::__construct();
        $this->_dir = $dir;
        $this->directories()
             ->in($this->_dir)
             ->ignoreDotFiles(true);
    }
    
    /**
     * Re-builds the collection when unserialized
     */
    public function __wakeup()
    {
        self::__construct($this->_dir);
    }

    /**
     * Finder::getIterator() alias
     *
     * @return \ArrayIterator $sets
     */
    public function getAll()
    {
        $sets = new \ArrayIterator();
        $iterator = $this->getIterator();
        
        foreach ($iterator as $set) {
            $sets->append(new Set($set));
        }
        
        return $sets;
    }
    
    /**
     * Set getter by name
     * 
     * @param string $name The set filename
     * @return Smak\Portfolio\Set | null
     */
    public function getByName($name)
    {
        parent::getByName($name);

        foreach ($this->getIterator() as $file_info) {
            if ($name == $file_info->getFilename()) {
                return new Set($file_info);
            }
        }
    }

    /**
     * Returns this instance as a Set
     *
     * @return \Smak\Portfolio\Set
     */
    public function asSet()
    {
        return new Set(new \SplFileInfo($this->_dir));
    }
}
