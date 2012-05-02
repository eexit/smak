<?php

namespace Smak\Portfolio;

/**
 * SortHelper.php
 * 
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2012, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class SortHelper
{
    /**
     * Sorts files by modification time (newest first)
     * 
     * @static
     * @return \Closure
     */
    public static function byNewest()
    {
        return function(\SplFileInfo $a, \SplFileInfo $b) {
            return !($a->getMTime() > $b->getMTime());
        };
    }
    
    /**
     * Sorts files by modification time (oldest first)
     * 
     * @static
     * @return \Closure
     */
    public static function byOldest()
    {
        return function(\SplFileInfo $a, \SplFileInfo $b) {
            return ($a->getMTime() > $b->getMTime());
        };
    }

    /**
     * Sorts files by reverse natural order
     *
     * @static
     * @return \Closure
     */
    public static function reverseName()
    {
        return function (\SplFileInfo $a, \SplFileInfo $b) {
            return strcmp($b->getRealPath(), $a->getRealPath());
        };
    }
}