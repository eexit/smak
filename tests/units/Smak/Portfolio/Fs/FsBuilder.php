<?php

namespace tests\units\Smak\Portfolio\Fs;

/**
 * FsBuilder.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FsBuilder
{
    /**
     * FS root dir
     */
    private $root;
    
    /**
     * FS dir and file tree
     */
    private $tree = array();
    
    /**
     * Is the FS built?
     */
    private $is_built = false;
    
    /**
     * Files have to have different modif time each other
     */
    private $diff_time = false;
    
    /**
     * Class constructor
     * 
     * @param array $tree File tree to build
     * @param [string $dir Root FS dir]
     */
    public function __construct(array $tree, $dir = null)
    {
        if (! $dir) {
            $dir = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'eexit-smak'; 
        }
        
        $this->tree = $tree;
        $this->prepareRoot($dir);
    }
    
    /**
     * FS tree builder
     * 
     * @return
     */
    public function build()
    {
        $dir = __DIR__;

        chdir($this->getRoot());

        if ($this->buildTree($this->tree)) {
            $this->is_built = true;
        }

        chdir($dir);
        
        return $this;
    }
    
    /**
     * Returns the state of tree building
     * 
     * @return bool
     */
    public function isBuilt()
    {
        return $this->is_built;
    }
    
    /**
     * Specifies if each tree component must have different time generation
     * 
     * @param bool $flag
     * @return
     */
    public function setDiffTime($flag)
    {
        $this->diff_time = (bool) $flag;

        return $this;
    }
    
    /**
     * FS tree cleaner
     * 
     * @return
     */
    public function clear()
    {
        $root = $this->getRoot();

        if (empty($root) || DIRECTORY_SEPARATOR == $root || DIRECTORY_SEPARATOR . '*' == $root) {
            return;
        }

        @shell_exec('rm -rf ' . $root);
        
        return $this;
    }
    
    /**
     * FS tree getter
     * 
     * @return array
     */
    public function getTree()
    {
        return $this->tree;
    }
    
    /**
     * FS root dir getter
     * 
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Provides a random timestamp
     *
     * @return int
     */
    private function getRandomTimestamp()
    {
        return mt_rand(1325372400, 1356994800);
    }

    private function prepareRoot($dir)
    {
        if (! is_dir($dir)) {
            mkdir($dir);
        }

        if (! is_dir($dir) || ! is_writable($dir)) {
            throw new \InvalidArgumentException('Filesystem root dir does not exists or cannot be written!');
        }

        $this->root = $dir;
    }
    
    /**
     * FS tree internal builder (recursive calls)
     * 
     * @param array $root Top current branch root
     * @return bool
     */
    private function buildTree($root)
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
                
                if ($this->diff_time && ! touch($dir, $this->getRandomTimestamp(), $this->getRandomTimestamp())) {
                    return false;
                }

                if (! $this->buildTree($file)) {
                    return false;
                }
                chdir('..');
            } else {
                if ($this->diff_time && ! touch($file, $this->getRandomTimestamp(), $this->getRandomTimestamp())) {
                    return false;
                } elseif (! touch($file)) {
                    return false;
                }
            }
        }

        return true;
    }
}
