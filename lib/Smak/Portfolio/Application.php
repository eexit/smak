<?php

namespace Smak\Portfolio;

use Symfony\Component\Finder\Finder;

/**
 * Application.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2011, Joris Berthelot
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
        return $this->getIterator();
    }
    
    /**
     * Set getter
     * 
     * @param string $setName Set name
     */
    public function getSet($setName)
    {
        foreach ($this->getIterator() as $fileInfo) {
            if ($setName == $fileInfo->getFileName()) {
                return $fileInfo;
            }
        }
    }
}
