<?php

namespace tests;

/**
 * Fs.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Fs
{
    /**
     * FS root dir
     */
    private $_root;
    
    /**
     * FS dir and file tree
     */
    private $_tree = array();
    
    /**
     * Is the FS built?
     */
    private $_built = false;
    
    /**
     * Files have to have different modif time each other
     */
    private $_diffTime = false;
    
    /**
     * Class constructor
     * 
     * @param string $dir Root FS dir
     * @param array [$tree Optional FS tree]
     */
    public function __construct($dir, array $tree = null)
    {
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \InvalidArgumentException('Filesystem root dir does not exists or cannot be written!');
        }
        
        $this->_root = $dir;
        
        if (!is_null($tree)) {
            $this->_tree = $tree;
        }
    }
    
    /**
     * FS tree builder
     * 
     * @return
     */
    public function build()
    {
        chdir($this->_root);
        if ($this->_buildTree($this->_tree)) {
            $this->_built = true;
        }
        
        return $this;
    }
    
    /**
     * Returns the state of tree building
     * 
     * @return bool
     */
    public function isBuilt()
    {
        return $this->_built;
    }
    
    /**
     * Specifies if each tree component must have different time generation
     * 
     * @param bool $flag
     */
    public function setDiffTime($flag)
    {
        $this->_diffTime = (bool) $flag;
    }
    
    /**
     * FS tree cleaner
     * 
     * @return
     */
    public function clear()
    {
        @shell_exec('rm -rf ' . implode(' ', array_keys($this->_tree)));
        
        return $this;
    }
    
    /**
     * FS tree getter
     * 
     * @return array
     */
    public function getTree()
    {
        return $this->_tree;
    }
    
    /**
     * FS root dir getter
     * 
     * @return string
     */
    public function getRoot()
    {
        return $this->_root;
    }
    
    /**
     * FS tree internal builder (recursive calls)
     * 
     * @param array $root Top current branch root
     * @return bool
     */
    private function _buildTree($root)
    {
        foreach ($root as $dir => $files) {
            if (is_array($files)) {
                if (!is_dir($dir)) {
                    if (!mkdir($dir)) {
                        return false;
                    }
                }
                
                if (!chdir($dir)) {
                    return false;
                }
                
                if ($this->_diffTime && !touch($dir, time() - 3600)) {
                    return false;
                }
                
                if (!$this->_buildTree($files)) {
                    return false;
                }
                chdir('..');
            } else {
                if ($this->_diffTime && !touch($files, time() - 3600)) {
                    return false;
                } elseif (!touch($files)) {
                    return false;
                }
            }
        }
        return true;
    }
}
?>