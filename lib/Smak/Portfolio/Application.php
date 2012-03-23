<?php

namespace Smak\Portfolio;

use Smak\Portfolio\Set;
use Symfony\Component\Finder\Finder;

/**
 * Application.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Application extends Finder implements \Countable
{
    /**
     * Class constructor
     * 
     * @param string $dir Set parent directory
     */
    public function __construct($dir = __DIR__)
    {
        parent::create();
        $this->directories()->in($dir)->ignoreDotFiles(true);
    }
    
    /**
     * Mandatory count method
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }
    
    /**
     * Finder::getIterator() alias
     */
    public function getSets()
    {
        $sets = new \ArrayIterator();
        $iterator = $this->getIterator();
        
        foreach ($iterator as $set) {
            $sets->append(new Set($set));
        }
        
        return $sets;
    }
    
    /**
     * Set getter
     * 
     * @param string $set_name Set name
     */
    public function getSet($set_name)
    {
        foreach ($this->getIterator() as $file_info) {
            if ($set_name == $file_info->getFileName()) {
                return new Set($file_info);
            }
        }
    }
}
