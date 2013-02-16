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
    public function __construct(array $tree = null, $dir = null)
    {
        if (! $dir) {
            $dir = realpath(sys_get_temp_dir());
        }

        if (! is_dir($dir) || ! is_writable($dir)) {
            throw new \InvalidArgumentException('Filesystem root dir does not exists or cannot be written!');
        }
        
        $this->_root = $dir;
        
        if (! null == $tree) {
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
     * @return
     */
    public function setDiffTime($flag)
    {
        $this->_diffTime = (bool) $flag;

        return $this;
    }
    
    /**
     * FS tree cleaner
     * 
     * @return
     */
    public function clear()
    {
        @shell_exec('rm -rf ' . $this->getRoot() . '/*');
        
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
     * Provides a random timestamp
     *
     * @return int
     */
    private function _getRandomTimestamp()
    {
        return mt_rand(1325372400, 1356994800);
    }
    
    /**
     * FS tree internal builder (recursive calls)
     * 
     * @param array $root Top current branch root
     * @return bool
     */
    private function _buildTree($root)
    {
        foreach ($root as $dir => $file) {
            if (is_array($file)) {
                if (! is_dir($dir)) {
                    if (! mkdir($dir)) {
                        return false;
                    }
                }
                
                if (! chdir($dir)) {
                    return false;
                }
                
                if ($this->_diffTime && ! touch($dir, $this->_getRandomTimestamp(), $this->_getRandomTimestamp())) {
                    return false;
                }

                if (! $this->_buildTree($file)) {
                    return false;
                }
                chdir('..');
            } else {
                if ($this->_diffTime && ! touch($file, $this->_getRandomTimestamp(), $this->_getRandomTimestamp())) {
                    return false;
                } elseif (! touch($file)) {
                    return false;
                }
            }
        }

        return true;
    }
}
