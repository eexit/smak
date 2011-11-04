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
        $this->files()
             ->name(self::PHOTO_PATTERN)
             ->in($setInfo->getRealPath())
             ->ignoreDotFiles(true);
    }
    
    /**
     * Mandatory count method
     * 
     * @return int
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }
    
    /**
     * Set photo content getter
     * 
     * @return ArrayIterator
     */
    public function getPhotos()
    {
        $iterator = $this->getIterator();
        $photos = new \ArrayIterator();
        
        foreach ($iterator as $photo) {
            $photos->append($photo->getFileInfo('\Smak\Portfolio\Photo'));
        }
        
        return $photos;
    }
    
    /**
     * Photo getter by ID
     * 
     * @param int $id The photo position
     * @return Smak\Portfolio\Photo
     * @throws InvalidArgumentException, OutOfRangeException
     */
    public function getPhotoById($id)
    {
        if (!is_int($id) || 0 > $id) {
            throw new \InvalidArgumentException('Photo ID argument must an integer >=  0!');
        }
        
        if (!$this->getPhotos()->offsetExists($id)) {
            throw new \OutOfRangeException(sprintf('Photo ID #%d does not exist!', $id));
        }
        
        return $this->getPhotos()->offsetGet($id);
    }
    
    /**
     * Photo getter by name (filename without extension)
     * 
     * @param string $name The photo filename (w/o extension)
     * @return Smak\Portfolio\Photo
     * @throws InvalidArgumentException, UnexpectedValueException
     */
    public function getPhotoByName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('Photo name argument must a non empty string!');
        }
        
        foreach ($this->getPhotos() as $photo) {
            if ($name === preg_replace(self::PHOTO_PATTERN, null, $photo->getFilename())) {
                
                return $photo;
            }
        }
        
        throw new \UnexpectedValueException(sprintf('Photo "%s" does not exist!', $name));
    }
    
    /**
     * Gets the first photo of set
     * 
     * @return Smak\Portfolio\Photo
     */
    public function getFirst()
    {
        $iterator = iterator_to_array($this->getPhotos());
        
        return array_shift($iterator);
    }
    
    /**
     * Gets the last photo of set
     * 
     * @return Smak\Portfolio\Photo
     */
    public function getLast()
    {
        $iterator = iterator_to_array($this->getPhotos());
        
        return array_pop($iterator);
    }
    
    /**
     * Photo set info file getter
     * 
     * @return SplFileInfo
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
     * 
     * @return SplFileInfo
     */
    public function getSplInfo()
    {
        return $this->_setInfo;
    }
    
    /**
     * Sorts files by modification time (newest first)
     * 
     * @return Smak\Portfolio\Set
     */
    public function sortByNewest()
    {
        return parent::sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            return $a->getMTime() > $b->getMTime() ? -1 : 1;
        });
    }
    
    /**
     * Sorts files by modification time (oldest first)
     * 
     * @return Smak\Portfolio\Set
     */
    public function sortByOldest()
    {
        return parent::sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            return $a->getMTime() < $b->getMTime() ? -1 : 1;
        });
    }
}