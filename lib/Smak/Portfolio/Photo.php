<?php

namespace Smak\Portfolio;

/**
 * Photo.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2011, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Photo extends \SplFileInfo
{
    /**
     * Photo file type (IMAGE_XXX)
     */
    protected $_type;
    
    /**
     * Photo width
     */
    protected $_width;
    
    /**
     * Photo height
     */
    protected $_height;
    
    /**
     * HTML photo size attribute
     */
    protected $_html_attr;
    
    /**
     * Human reading photo size
     */
    protected $_size;
    
    /**
     * Class constructor
     * 
     * @param string $file_name The file name (@see \SplFileInfo::__construct())
     * @throws \RuntimeException
     */
    public function __construct($file_name)
    {
        parent::__construct($file_name);
        
        if (!extension_loaded('gd')) {
            throw \RuntimeException('GD PHP extension not loaded!');
        }
        
        list(
            $this->_width,
            $this->_height,
            $this->_type,
            $this->_html_attr
        ) = getimagesize($this->getRealPath());
        $this->_size = $this->_formatSize($this->getSize());
    }
    
    /**
     * Photo type getter
     * 
     * @return string
     */
    public function getPhotoType()
    {
        return $this->_type;
    }
    
    /**
     * Photo width getter
     * 
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }
    
    /**
     * Photo height getter
     * 
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }
    
    /**
     * HTML photo size attribute getter
     * 
     * @return string
     */
    public function getHtmlAttr()
    {
        return $this->_html_attr;
    }
    
    /**
     * Human photo size getter
     * 
     * @return string
     */
    public function getHRSize()
    {
        return $this->_size;
    }
    
    /**
     * Formats the file size for easy-human-reading
     * 
     * @param int $size The file size in bytes
     * @throws \InvalidArgumentException
     * @return string
     */
    private function _formatSize($size)
    {
        if (0 > $size) {
            throw \InvalidArgumentException('File size cannot be lesser than 0 byte!');
        }
        
        if ($size < 1e3) { // 0 octets > 9999 octets
            $sext = 'b';
        } elseif ($size >= 1e3 && $size < 1e6) { // 1 Ko > 9999 Ko
            $size = round($size / 1e3);
            $sext = 'Ko';
        } elseif ($size >= 1e6 && $size < 1e7) { // 1 Mo > 9 Mo
            $size = round($size / 1e6, 3);
            $sext = 'Mo';
        } elseif ($size >= 1e7 && $size < 1e8) { // 10 Mo > 99 Mo
            $size = round($size / 1e6, 2);
            $sext = 'Mo';
        } elseif ($size >= 1e8 && $size < 1e9) { // 100 Mo > 999 Mo
            $size = round($size / 1e6, 1);
            $sext = 'Mo';
        } elseif ($size >= 1e9) { // 1000 Mo et +
            $size = round($size / 1e6);
            $sext = 'Go';
        }
        
        return sprintf('%d %s', $size, $sext);
    }
}
