<?php

namespace Smak\Portfolio;

use Symfony\Component\Finder\Finder;

/**
 * Set.php
 * 
 * @author Joris Berthelot <joris.berthelot@gmail.com>
 * @copyright Copyright (c) 2011, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Set extends Finder implements \Countable
{
    /**
     * Allowed photography file extensions
     */
    const PHOTO_PATTERN = '/\.(jpe?g|png)$/i';
    
    /**
     * Set info file extension
     */
    const INFO_EXT = '.twig';
    
    /**
     * Set name
     */
    public $name = null;
    
    /**
     * Set technical info (instance of \SplFileInfo)
     */
    protected $_setInfo;
    
    /**
     * Class constructor
     * 
     * @param \SplFileInfo $setInfo Set file info
     */
    public function __construct(\SplFileInfo $setInfo)
    {
        parent::create();
        $this->_setInfo = $setInfo;
        $this->name = $setInfo->getFileName();
        $this->files()->name(self::PHOTO_PATTERN)->in($setInfo->getRealPath())->ignoreDotFiles(true);
    }
    
    /**
     * Mandatory count method
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }
    
    /**
     * Set photo content getter
     */
    public function getPhotos()
    {
        return $this->getIterator();
    }
    
    /**
     * Set info file getter
     */
    public function getInfo()
    {
        $infoFile = $this->_setInfo->getRealPath() . DIRECTORY_SEPARATOR . strtolower($this->name) . self::INFO_EXT;
        if (is_file($infoFile)) {
            return new \SplFileInfo($infoFile);
        }
    }
    
    /**
     * SPL set info getter
     */
    public function getSplInfo()
    {
        return $this->_setInfo;
    }
    
    /**
     * Sorts files by modification time (newest first)
     */
    public function sortByNewest()
    {
        return parent::sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            return $a->getMTime() > $b->getMTime() ? -1 : 1;
        });
    }
    
    /**
     * Sorts files by modification time (oldest first)
     */
    public function sortByOldest()
    {
        return parent::sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            return $a->getMTime() < $b->getMTime() ? -1 : 1;
        });
    }
}