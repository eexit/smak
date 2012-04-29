<?php

namespace Smak\Portfolio;

/**
 * Set.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Set extends Portfolio
{
    /**
     * Allowed set template file extensions
     */
    protected $_template_ext = array('.html.twig');
    
    /**
     * Allowed set photography file extensions
     */
    protected $_allowed_ext = array('.jpg', '.jpeg', '.jpf', '.png');
    
    /**
     * Set name
     */
    public $name = null;
    
    /**
     * Set real path
     */
    protected $_set_path;

    /**
     * Helpers
     */
    public $helpers = array();
    
    /**
     * Class constructor
     * 
     * @param \SplFileInfo $set_info Set file info
     */
    public function __construct(\SplFileInfo $set_info)
    {
        parent::create();
        $this->_set_path = $set_info->getRealPath();
        $this->name = $set_info->getFilename();
        $this->files()
             ->in($set_info->getRealPath())
             ->ignoreDotFiles(true);
             
        foreach ($this->_allowed_ext as $ext) {
            $this->name(sprintf('/%s$/i', $ext));
        }
    }

    /**
     * Returns needed parameters for serialization
     *
     * @return array
     */
    public function __sleep()
    {
        return array('name', '_set_path', 'helpers');
    }

    /**
     * Re-builds the set when unserialized
     */
    public function __wakeup()
    {
        self::__construct($this->getSplInfo());
    }
    
    /**
     * Set photo content getter
     * 
     * @return \ArrayIterator $photos
     */
    public function getAll()
    {
        $photos = new \ArrayIterator();
        $iterator = $this->getIterator();
        
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
    public function getExtensions()
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
    public function setExtensions(array $new_ext)
    {
        if (empty($new_ext)) {
            throw new \InvalidArgumentException('New allowed photo extension array must not be empty!');
        }
        
        $this->_allowed_ext = $new_ext;
        
        return $this;
    }
    
    /**
     * Set template file extension getter
     * 
     * @return array
     */
    public function getTemplateExtensions()
    {
        return $this->_template_ext;
    }
    
    /**
     * Set template file extension setter
     * 
     * @param array $new_ext New array of template file extensions
     * @return Smak\Portfolio\Set
     * @throws \InvalidArgumentException
     */
    public function setTemplateExtensions(array $new_ext)
    {
        if (empty($new_ext)) {
            throw new \InvalidArgumentException('New template file extension array must not be empty!');
        }
        
        $this->_template_ext = $new_ext;
        
        return $this;
    }
    
    /**
     * Photo getter by name (filename without extension)
     * 
     * @param string $name The photo filename (w/o extension)
     * @return Smak\Portfolio\Photo | null
     */
    public function getByName($name)
    {
        parent::getByName($name);
        
        foreach ($this->getAll() as $photo) {
            foreach ($this->_allowed_ext as $ext) {
                if ($name === $photo->getBasename($ext)) {

                    return $photo;
                }
            }
        }
    }
    
    /**
     * Photo set template info file getter
     * 
     * @return SplFileInfo
     */
    public function getTemplate()
    {
        foreach ($this->_template_ext as $ext) {
            
            $info_file = $this->getSplInfo()->getRealPath()
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
     * @return \SplFileInfo
     */
    public function getSplInfo()
    {
        return new \SplFileInfo($this->_set_path);
    }

    /**
     * Returns this instance as a Collection
     *
     * @return \Smak\Portfolio\Collection
     */
    public function asCollection()
    {
        return new Collection($this->getSplInfo()->getRealPath());
    }
}
