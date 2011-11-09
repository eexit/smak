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
     * Allowed set info file extensions
     */
    protected $_info_ext = array('.twig');
    
    /**
     * Allowed set photography file extensions
     */
    protected $_allowed_ext = array('.jpg', '.jpeg', '.jpf', '.png');
    
    /**
     * Set name
     */
    public $name = null;
    
    /**
     * Set technical info (instance of \SplFileInfo)
     */
    protected $_set_info;
    
    /**
     * Class constructor
     * 
     * @param \SplFileInfo $setInfo Set file info
     */
    public function __construct(\SplFileInfo $set_info)
    {
        parent::create();
        $this->_set_info = $set_info;
        $this->name = $set_info->getFileName();
        $this->files()
             ->in($set_info->getRealPath())
             ->ignoreDotFiles(true);
             
        foreach ($this->_allowed_ext as $ext) {
            $this->name(sprintf('/\%s$/i', $ext));
        }
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
     * Allowed photo extension getter
     * 
     * @return array
     */
    public function getPhotoExtensions()
    {
        return $this->_allowed_ext;
    }
    
    /**
     * Allowed photo extension setter
     * 
     * @param array $new_ext New array of photo extensions
     * @return Smak\Portfolio\Set
     * @throws \InvalidArgumentException
     */
    public function setPhotoExtensions(array $new_ext)
    {
        if (empty($new_ext)) {
            throw new \InvalidArgumentException('New allowed photo extension array must not be empty!');
        }
        
        $this->_allowed_ext = $new_ext;
        
        return $this;
    }
    
    /**
     * Set info file extension getter
     * 
     * @return array
     */
    public function getInfoExtensions()
    {
        return $this->_info_ext;
    }
    
    /**
     * Set info file extension setter
     * 
     * @param array $new_ext New array of info file extensions
     * @return Smak\Portfolio\Set
     * @throws \InvalidArgumentException
     */
    public function setInfoExtensions(array $new_ext)
    {
        if (empty($new_ext)) {
            throw new \InvalidArgumentException('New info file extension array must not be empty!');
        }
        
        $this->_info_ext = $new_ext;
        
        return $this;
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
            foreach ($this->_allowed_ext as $ext) {
                if ($name === $photo->getBasename($ext)) {

                    return $photo;
                }
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
        foreach ($this->_info_ext as $ext) {
            
            $info_file = $this->_set_info->getRealPath()
                . DIRECTORY_SEPARATOR
                . strtolower($this->name)
                . $ext;
                
            if (is_file($info_file)) {

                return new \SplFileInfo($info_file);
            }
        }
    }
    
    /**
     * SPL set info getter
     * 
     * @return SplFileInfo
     */
    public function getSplInfo()
    {
        return $this->_set_info;
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