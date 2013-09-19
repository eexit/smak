<?php

namespace Smak\Fcms\Loader;

use Smak\Fcms\LoaderInterface;
use Symfony\Component\Finder\Finder;

/**
 * Local.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Local extends Finder implements LoaderInterface
{
    protected $_options;

    public function __construct(array $options = array())
    {
        parent::__construct();

        $this->_options = array_merge(array(
            'content_path'     => __DIR__,
            'default_filename' => 'index',
            'file_extension'   => null
        ), $options);

        $this
            ->files()
            ->ignoreDotFiles(true)
            ->in($this->_options['dir']);
    }

    public function find($file_uri)
    {
        $path = sprintf("%s%s", $file_uri, $this->_options['file_extension']);
        $depth = substr_count($file_uri, DIRECTORY_SEPARATOR);

        $this->path($path)->depth($depth);

        if (0 == count($this)) {
            $path = sprintf("%s%s%s%s",
                $file_uri,
                DIRECTORY_SEPARATOR,
                $this->_options['default_filename'],
                $this->_options['file_extension']
            );
            $this->path($path)->depth(++$depth);
        }

        if (0 == count($this)) {
            return false;
        }

        $files = iterator_to_array($this->getIterator());

        return array_shift($files);
    }
}

